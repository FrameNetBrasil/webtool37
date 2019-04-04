<?php

class MainController extends MController
{

    private $idLanguage;

    public function init()
    {
        parent::init();
        $this->idLanguage = Manager::getSession()->idLanguage;
    }

    public function cxnTree()
    {
        $structure = Manager::getAppService('structurecxn');
        if ($this->data->id == '') {
            $children = $structure->listCxnLanguage($this->data);
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

    public function grapher()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $domain = new fnbr\models\Domain();
        $this->data->domain = $domain->gridDataAsJson($domain->listForSelection(), true);
        $this->render();
    }

    public function query()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $domain = new fnbr\models\Domain();
        $this->data->domain = $domain->gridDataAsJson($domain->listForSelection(), true);
        $this->render();
    }

}
