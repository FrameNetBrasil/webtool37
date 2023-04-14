<?php





class SemanticTypeController extends MController
{

    private $idLanguage;

    public function init()
    {
        parent::init();
        $this->idLanguage = \Manager::getSession()->idLanguage;
    }

    public function main()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $this->render();
    }

    public function semanticTypeTree()
    {
        $structure = Manager::getAppService('structuresemantictype');
        if ($this->data->id == '') {
            $children = $structure->listDomains($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'SemanticTypes',
                'children' => $children
            ];
            $json = json_encode([$data]);
        } elseif ($this->data->id{0} == 'd') {
            $children = $structure->listSemanticTypesRoot($this->data, substr($this->data->id, 1), $this->idLanguage);
            $json = json_encode($children);
        } elseif ($this->data->id{0} == 't') {
            $children = $structure->listSemanticTypesChildren(substr($this->data->id, 1), $this->idLanguage);
            $json = json_encode($children);
        }
        $this->renderJson($json);
    }
    
    public function formNewSemanticType(){
        $nodeId = $this->data->id;
        if ($nodeId{0} == 'd') {
            $this->data->idDomain = substr($this->data->id, 1);
        } else {
            $this->data->idSuperType = substr($this->data->id, 1);
        }
        $this->data->save = "@structure/semantictype/newSemanticType|formNewSemanticType";
        $this->data->close = "!$('#formNewSemanticType_dialog').dialog('close');";
        $this->data->title = _M('new fnbr\models\SemanticType');
        $this->render();
    }
    
    public function formUpdateSemanticType(){
        $model = new fnbr\models\SemanticType($this->data->id);
        $this->data->object = $model->getData();
        $this->data->save = "@structure/semantictype/updateSemanticType|formUpdateSemanticType";
        $this->data->close = "!$('#formUpdateSemanticType_dialog').dialog('close');";
        $this->data->title = 'SemanticType: ' . $model->getEntry() . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function newSemanticType()
    {
        try {
            $model = new fnbr\models\SemanticType();
            $this->data->semantictype->entry = 'sty_' . $this->data->semantictype->entry;
            $model->setData($this->data->semantictype);
            $model->save($this->data->semantictype);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->semantictype->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }
    
    public function updateSemanticType()
    {
        try {
            $model = new fnbr\models\SemanticType($this->data->semantictype->idSemanticType);
            $model->updateEntry($this->data->semantictype->entry);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->semantictype->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function deleteSemanticType()
    {
        try {
            $model = new fnbr\models\SemanticType($this->data->id);
            $model->delete();
            $this->renderPrompt('information', "Record removed.","structure.reloadSemanticType();");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }
    
    public function addEntitySemanticType() {
        try {
            $structure = Manager::getAppService('structuresemantictype');
            $structure->addEntitySemanticType($this->data->idEntity, $this->data->idSemanticType);
            $this->renderPrompt('information', "Ok","$('#{$this->data->idGrid}').datagrid('reload');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }    

    public function delEntitySemanticType() {
        try {
            $structure = Manager::getAppService('structuresemantictype');
            $structure->delEntitySemanticType($this->data->idEntity, $this->data->toRemove);
            $this->renderPrompt('information', "Ok","$('#{$this->data->idGrid}').datagrid('reload');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }    
    
}
