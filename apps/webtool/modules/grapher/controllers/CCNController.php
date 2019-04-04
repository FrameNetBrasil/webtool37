<?php

class CCNController extends MController
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
        $this->data->relationData = $grapher->getRelationDataCCN();
        $this->data->relationEntry = MUtil::php2js($this->data->relationData);
        $this->render();
    }

    public function cxnTree()
    {
        $structure = Manager::getAppService('structurecxn');
        if ($this->data->id == '') {
            $children = $structure->listCxnLanguageEntity($this->data);
            $data = (object) [
                'id' => 'root',
                'state' => 'open',
                'text' => 'Constructions',
                'children' => $children
            ];
            $json = json_encode([$data]);
        } elseif ($this->data->id{0} == 'l') {
            $json = $structure->listCxnLanguage($this->data, substr($this->data->id, 1));
        } elseif ($this->data->id{0} == 'c') {
            $json = $structure->listCEsConstraintsEvokesCX(substr($this->data->id, 1), $this->idLanguage);
        } elseif ($this->data->id{0} == 'e') {
            $json = $structure->listConstraintsEvokesCE(substr($this->data->id, 1), $this->idLanguage);
        } elseif ($this->data->id{0} == 'x') {
            $json = $structure->listConstraintsCN(substr($this->data->id, 1), $this->idLanguage);
        } elseif ($this->data->id{0} == 'n') {
            $json = $structure->listConstraintsCNCN(substr($this->data->id, 1), $this->idLanguage);
        }
        $this->renderJson($json);
    }
    
}
