<?php





class CxnController extends MController
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
        $this->render();
    }

    public function cxnTree()
    {
        $editor = Manager::getAppService('visualeditor');
        if ($this->data->id == '') {
            $children = $editor->listCxns($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'Constructions',
                'children' => $children
            ];
            $json = json_encode([$data]);
        }
        $this->renderJson($json);
    }
    
    public function workingArea() {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE);
        $editor = Manager::getAppService('visualeditor');
        $this->data->cxns = $editor->getCxns();
        $this->data->relationEntry = $editor->getCxnRelationEntry();
        $this->render();
    }
    
    public function getCxnRelations(){
        $editor = Manager::getAppService('visualeditor');
        $this->data->relations = $editor->getCxnRelations($this->data->id);
        $this->renderJSON($this->data->relations);
    }

    public function getCE(){
        $editor = Manager::getAppService('visualeditor');
        $this->data->ces = $editor->getCEs($this->data->id);
        $this->renderJSON($this->data->ces);
    }

    public function getCERelations(){
        $editor = Manager::getAppService('visualeditor');
        $this->data->relations = $editor->getCERelations($this->data->idCxn1,$this->data->idCxn2,$this->data->idType);
        $this->renderJSON($this->data->relations);
    }
    
    public function saveCxnRelation(){
        $isMaster = Manager::checkAccess('MASTER', A_EXECUTE);
        $editor = Manager::getAppService('visualeditor');
        if ($isMaster) {
            $editor->updateCxnRelation($this->data->graphJson);
            $editor->deleteCxnRelation($this->data->linksRemoved);
            $this->renderPrompt('info','Ok');
        } else {
            $this->renderPrompt('error','Error');
        }
    }

    public function saveCERelation(){
        $isMaster = Manager::checkAccess('MASTER', A_EXECUTE);
        $editor = Manager::getAppService('visualeditor');
        if ($isMaster) {
            $editor->updateCERelation($this->data->graphceJson);
            $editor->deleteCERelation($this->data->linksceRemoved);
            $this->renderPrompt('info','Ok');
        } else {
            $this->renderPrompt('error','Error');
        }
    }

}
