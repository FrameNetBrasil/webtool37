<?php

class GrapherController extends MController
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
        Manager::getSession()->idDomain = $this->data->idDomain;
        $this->data->relationData = $grapher->getRelationData();
        $this->data->relationEntry = MUtil::php2js($this->data->relationData);
        $this->render();
    }

    public function frameTree()
    {
        $this->data->idDomain = Manager::getSession()->idDomain;
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
