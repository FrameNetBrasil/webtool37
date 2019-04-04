<?php


class RelationTypeController extends MController
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

    public function modelTree()
    {
        $structure = Manager::getAppService('structurerelationtype');
        if ($this->data->id == '') {
            $children = $structure->listAll($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'Relation Types',
                'children' => $children
            ];
            $json = json_encode([$data]);
        }
        $this->renderJson($json);
    }

    public function formNewRelationType()
    {
        $nodeId = $this->data->id;
        if ($nodeId{0} == 'm') {
            $this->data->id = substr($this->data->id, 1);
        }
        $this->data->save = "@structure/relationtype/newRelationType|formNewRelationType";
        $this->data->close = "!$('#formNew_dialog').dialog('close');";
        $this->data->title = _M('new fnbr\models\Relation Type');
        $this->render();
    }

    public function formUpdateRelationType()
    {
        $model = new fnbr\models\RelationType($this->data->id);
        $this->data->object = $model->getData();
        $this->data->save = "@structure/relationtype/updateRelationType|formUpdateRelationType";
        $this->data->close = "!$('#formUpdate_dialog').dialog('close');";
        $this->data->title = 'Relation Type: ' . $model->getEntry() . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function newRelationType()
    {
        try {
            $model = new fnbr\models\RelationType();
            $this->data->relationtype->entry = 'rel_' . str_replace('rel_', '', strtolower($this->data->relationtype->entry));
            $this->data->relationtype->nameEntity1 = $this->data->relationtype->entry . '_nameentity1';
            $this->data->relationtype->nameEntity2 = $this->data->relationtype->entry . '_nameentity2';
            $model->setData($this->data->relationtype);
            $model->save();
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->relationtype->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function updateRelationType()
    {
        try {
            $model = new fnbr\models\RelationType($this->data->relationtype->idRelationType);
            $this->data->relationtype->entry = 'rel_' . str_replace('rel_', '', strtolower($this->data->relationtype->entry));
            $model->updateEntry($this->data->relationtype->entry);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->relationtype->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

}
