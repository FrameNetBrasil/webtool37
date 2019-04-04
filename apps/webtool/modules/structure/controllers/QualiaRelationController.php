<?php

class QualiaRelationController extends MController
{

    private $idLanguage;

    public function init()
    {
        parent::init();
        $this->idLanguage = Manager::getSession()->idLanguage;
    }

    public function main()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $this->data->isAnno = 'false';
        $this->render();
    }

    public function relationTree()
    {
        $structure = Manager::getAppService('structurequaliarelation');
        //if ($this->data->id == '') {
            $children = $structure->listAll($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'iconCls' => 'icon-blank fas fa-arrows-alt-h fa16px',
                'text' => 'Relations',
                'children' => $children
            ];
            $json = json_encode([$data]);
        //}
        $this->renderJson($json);
    }

    public function formNewRelation()
    {
        $this->data->save = "@structure/qualiarelation/newRelation|formNewRelation";
        $this->data->close = "!$('#formNew_dialog').dialog('close');";
        $this->data->title = _M('new Qualia Relation');
        $this->render();
    }

    public function formUpdateRelation()
    {
        $model = new fnbr\models\Qualia($this->data->id);
        $this->data->qualia = $model->getData();
        $this->data->save = "@structure/qualiarelation/updateRelation|formUpdateRelation";
        $this->data->close = "!$('#formUpdate_dialog').dialog('close');";
        $this->data->title = 'Relation: ' . $model->getName() . '  [' . $model->getEntry() . ']';
        $this->render();
    }

    public function newRelation()
    {
        try {
            $model = new fnbr\models\Qualia();
            $this->data->qualia->entry = 'qla_' . str_replace('qla_', '', strtolower($this->data->qualia->entry));
            $model->save($this->data->qualia);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->qualia->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function updateRelation()
    {
        try {
            $model = new fnbr\models\Qualia($this->data->qualia->idQualia);
            $this->data->qualia->entry = 'qla_' . str_replace('qla_', '', strtolower($this->data->qualia->entry));
            $model->updateEntry($this->data->qualia->entry);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->qualia->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

}
