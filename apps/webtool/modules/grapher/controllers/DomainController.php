<?php

class DomainController extends MController
{
    private $idLanguage;

    public function init()
    {
        Manager::checkLogin(false);
        $this->idLanguage = Manager::getConf('fnbr.lang');
        $msgDir = Manager::getAppPath('conf/report');
        Manager::$msg->file = 'messages.' . $this->idLanguage . '.php';
        Manager::$msg->addMessages($msgDir);
    }

    public function main()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $grapher = Manager::getAppService('grapher');
        $domain = new fnbr\models\Domain();
        $this->data->domain = $domain->gridDataAsJson($domain->listForSelection(), true);
        $this->data->relationData = $grapher->getDomainRelationData();
        $this->data->relationEntry = json_encode($this->data->relationData);
        $this->render();
    }

    public function frameTree()
    {
        //$this->data->idDomain = Manager::getSession()->idDomain;
        $grapher = Manager::getAppService('grapher');
        if ($this->data->id == '') {
            $children = $grapher->listFrames($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'Frames',
                'children' => $children
            ];
            $json = json_encode([$data]);
        }
        $this->renderJson($json);
    }
    
    public function cxnTree()
    {
        $grapher = Manager::getAppService('grapher');
        if ($this->data->id == '') {
            $children = $grapher->listCxns($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'CxNs',
                'children' => $children
            ];
            $json = json_encode([$data]);
        }
        $this->renderJson($json);
    }    
    
}
