<?php





class FrameController extends MController
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

    public function coreness()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $this->render();
    }

    
    public function frameTree()
    {
        $editor = Manager::getAppService('visualeditor');
        if ($this->data->id == '') {
            $children = $editor->listFrames($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'Frames',
                'children' => $children
            ];
            $json = json_encode([$data]);
        } elseif ($this->data->id{0} == 'f') {
            $json = $editor->listLUs(substr($this->data->id, 1), $this->idLanguage);
        }
        $this->renderJson($json);
    }
    
    public function workingArea() {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE);
        $editor = Manager::getAppService('visualeditor');
        $this->data->frames = $editor->getFrames();
        $this->data->relationEntry = $editor->getRelationEntry();
        $this->render();
    }
    
    public function wacoreness() {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE);
        $editor = Manager::getAppService('visualeditor');
        $this->data->frames = $editor->getFrames();
        $this->data->relationEntry = $editor->getFECoreRelationEntry();
        $this->render();
    }

    public function getFrameRelations(){
        $editor = Manager::getAppService('visualeditor');
        $this->data->relations = $editor->getRelations($this->data->id);
        $this->renderJSON($this->data->relations);
    }

    public function getFE(){
        $editor = Manager::getAppService('visualeditor');
        $this->data->fes = $editor->getFEs($this->data->id);
        $this->renderJSON($this->data->fes);
    }

    public function getFERelations(){
        $editor = Manager::getAppService('visualeditor');
        $this->data->relations = $editor->getFERelations($this->data->idFrame1,$this->data->idFrame2,$this->data->idType);
        $this->renderJSON($this->data->relations);
    }
    
    public function getFECore(){
        $editor = Manager::getAppService('visualeditor');
        $this->data->fes = $editor->getFECore($this->data->id);
        $this->renderJSON($this->data->fes);
    }

    public function getFECoreRelations(){
        $editor = Manager::getAppService('visualeditor');
        $this->data->relations = $editor->getFECoreRelations($this->data->id);
        $this->renderJSON($this->data->relations);
    }

    public function test() {
        $this->render();
    }

    public function saveFrameRelation(){
        $isMaster = Manager::checkAccess('MASTER', A_EXECUTE);
        $editor = Manager::getAppService('visualeditor');
        if ($isMaster) {
            $editor->updateFrameRelation($this->data->graphJson);
            $editor->deleteFrameRelation($this->data->linksRemoved);
            $this->renderPrompt('info','Ok');
        } else {
            $this->renderPrompt('error','Error');
        }
    }

    public function saveFERelation(){
        $isMaster = Manager::checkAccess('MASTER', A_EXECUTE);
        $editor = Manager::getAppService('visualeditor');
        if ($isMaster) {
            $editor->updateFERelation($this->data->graphfeJson);
            $editor->deleteFERelation($this->data->linksfeRemoved);
            $this->renderPrompt('info','Ok');
        } else {
            $this->renderPrompt('error','Error');
        }
    }

    public function saveFECoreRelation(){
        $isMaster = Manager::checkAccess('MASTER', A_EXECUTE);
        $editor = Manager::getAppService('visualeditor');
        if ($isMaster) {
            $editor->updateFECoreRelation($this->data->graphJson);
            $editor->deleteFECoreRelation($this->data->linksRemoved);
            $this->renderPrompt('info','Ok');
        } else {
            $this->renderPrompt('error','Error');
        }
    }
    
}
