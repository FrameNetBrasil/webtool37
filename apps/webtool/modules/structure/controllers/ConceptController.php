<?php

class ConceptController extends MController
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

    public function conceptTree()
    {
        $structure = Manager::getAppService('structureconcept');
        if ($this->data->id == '') {
            $children = $structure->listConceptsRoot($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'Concepts',
                'children' => $children
            ];
            $json = json_encode([$data]);
        } elseif ($this->data->id{0} == 'c') {
            $children = $structure->listConceptsChildren(substr($this->data->id, 1), $this->idLanguage);
            $json = json_encode($children);
        } elseif ($this->data->id{0} == 'e') {
            $children = $structure->listConceptElements(substr($this->data->id, 1), $this->idLanguage);
            $json = json_encode($children);
        }
        $this->renderJson($json);
    }

    public function formNewConcept()
    {
        $this->data->idSuperType = ($this->data->id == 'root') ? 0 : substr($this->data->id, 1);
        $this->data->save = "@structure/concept/newConcept|formNewConcept";
        $this->data->close = "!$('#formNewConcept_dialog').dialog('close');";
        $this->data->title = _M('new Concept');
        $this->render();
    }

    public function formUpdateConcept()
    {
        $model = new fnbr\models\Concept($this->data->id);
        $this->data->object = $model->getData();
        $entry = $model->getEntryObject();
        $this->data->object->name = $entry->name;
        $this->data->object->description = $entry->description;
        $this->data->save = "@structure/concept/updateConcept|formUpdateConcept";
        $this->data->close = "!$('#formUpdateConcept_dialog').dialog('close');";
        $this->data->title = 'Concept: ' . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function newConcept()
    {
        try {
            $model = new fnbr\models\Concept();
            //$model->setData($this->data->concept);
            $model->save($this->data->concept);
            //$this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->semantictype->entry}');");
            $this->renderPrompt('information', 'OK');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function updateConcept()
    {
        try {
            $model = new fnbr\models\Concept($this->data->concept->idConcept);
            $model->update($this->data->concept);
            //$model->updateEntry($this->data->concept->entry);
            //$this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->semantictype->entry}');");
            $this->renderPrompt('information', 'OK');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function deleteConcept()
    {
        try {
            $model = new fnbr\models\Concept($this->data->id);
            $model->delete();
            $this->renderPrompt('information', "Record removed.", "structure.reloadConcept();");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function addConceptElement()
    {
        try {
            $model = new fnbr\models\Concept($this->data->concept->idConcept);
            //$model->setData($this->data->concept);
            $model->addConceptElement($this->data->concept);
            //$this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->semantictype->entry}');");
            $this->renderJson(json_encode(['success' => true]));
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function deleteConceptElement()
    {
        try {
            $model = new fnbr\models\Concept();
            $model->deleteConceptElement(substr($this->data->id, 1));
            $this->renderPrompt('information', "Record removed.", "structure.reloadConcept();");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function addEntityConcept()
    {
        try {
            $structure = Manager::getAppService('structureconcept');
            $structure->addEntityConcept($this->data->idEntity, $this->data->idConcept);
            $this->renderPrompt('information', "Ok", "$('#{$this->data->idGrid}').datagrid('reload');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function delEntityConcept()
    {
        try {
            $structure = Manager::getAppService('structureconcept');
            $structure->delEntityConcept($this->data->idEntity, $this->data->toRemove);
            $this->renderPrompt('information', "Ok", "$('#{$this->data->idGrid}').datagrid('reload');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

}
