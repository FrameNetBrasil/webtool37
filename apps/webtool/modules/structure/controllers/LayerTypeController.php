<?php




class LayerTypeController extends MController
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
        $structure = Manager::getAppService('structurelayertype');
        if ($this->data->id == '') {
            $children = $structure->listAll($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'Layer Types',
                'children' => $children
            ];
            $json = json_encode([$data]);
        } elseif ($this->data->id{0} == 'm') {
            $json = $structure->listGLByLayer(substr($this->data->id, 1), $this->idLanguage);
        }
        $this->renderJson($json);
    }

    public function formNewLayerType()
    {
        $nodeId = $this->data->id;
        if ($nodeId{0} == 'm') {
            $this->data->id = substr($this->data->id, 1);
        }
        $this->data->save = "@structure/layertype/newLayerType|formNewLayerType";
        $this->data->close = "!$('#formNew_dialog').dialog('close');";
        $this->data->title = _M('new fnbr\models\Layer Type');
        $this->render();
    }

    public function newLayerType()
    {
        try {
            $model = new fnbr\models\LayerType();
            $this->data->layertype->entry = 'lty_' . str_replace('lty_', '', strtolower($this->data->layertype->entry));
            $model->save($this->data->layertype);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->layertype->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formUpdateLayerType()
    {
        $model = new fnbr\models\LayerType($this->data->id);
        $this->data->object = $model->getData();
        $this->data->object->entry = str_replace('lty_', '', strtolower($this->data->object->entry));
        $this->data->save = "@structure/layertype/updateLayerType|formUpdateLayerType";
        $this->data->close = "!$('#formUpdate_dialog').dialog('close');";
        $this->data->title = 'Layer Type: ' . $model->getEntry() . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function updateLayerType()
    {
        try {
            $model = new fnbr\models\LayerType($this->data->layertype->idLayerType);
            $this->data->layertype->entry = 'lty_' . str_replace('lty_', '', strtolower($this->data->layertype->entry));
            $model->updateEntry($this->data->layertype->entry);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->layertype->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formNewGenericLabel()
    {
        $this->data->idLayerType = $this->data->id;
        $model = new fnbr\models\LayerType($this->data->idLayerType);
        $this->data->layerType = $model->getName();
        $this->data->save = "@structure/layertype/newGenericLabel|formNewGenericLabel";
        $this->data->close = "!$('#formNewGenericLabel_dialog').dialog('close');";
        $this->data->title = _M('new Label');
        $this->render();
    }

    public function newGenericLabel()
    {
        try {
            $model = new fnbr\models\GenericLabel();
            if (!$model->exists($this->data->genericlabel)) {
                $model->saveData($this->data->genericlabel);
                $this->renderPrompt('information', 'Label created.');
            } else {
                $this->renderPrompt('error', "Label already exists.");
            }
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formUpdateGenericLabel()
    {
        $this->data->idGenericLabel = $this->data->id;
        $gl = new fnbr\models\GenericLabel($this->data->idGenericLabel);
        $this->data->genericlabel = $gl->getData();
        $this->data->idLayerType = Manager::getContext()->get(1);
        $lt = new fnbr\models\LayerType($this->data->idLayerType);
        $this->data->layerType = $lt->getName();
        $this->data->save = "@structure/layertype/updateGenericLabel|formUpdateGenericLabel";
        $this->data->close = "!$('#formUpdateGenericLabel_dialog').dialog('close');";
        $this->data->title = _M('update Label');
        $this->render();
    }

    public function updateGenericLabel()
    {
        try {
            $model = new fnbr\models\GenericLabel($this->data->genericlabel->idGenericLabel);
            $model->saveData($this->data->genericlabel);
            $this->renderPrompt('information', 'Label updated.');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formDeleteGenericLabel()
    {
        $ok = "^structure/layertype/deleteGenericLabel/" . $this->data->id;
        $this->renderPrompt('confirmation', 'Warning! The Label will be removed! Continue?', $ok);
    }

    public function deleteGenericLabel()
    {
        try {
            $model = new fnbr\models\GenericLabel($this->data->id);
            if (!$model->inUse()) {
                $model->delete();
                $this->renderPrompt('information', 'Label deleted.', "!structure.reloadGenericLabel();");
            } else {
                $this->renderPrompt('error', "Label is in use.");
            }
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

}
