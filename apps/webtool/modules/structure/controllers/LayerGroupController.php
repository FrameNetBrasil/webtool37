<?php





class LayerGroupController extends MController
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
    
    public function lookupData(){
        $model = new fnbr\models\LayerGroup();
        $criteria = $model->listAll();
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }

    public function modelTree()
    {
        $structure = Manager::getAppService('structurelayergroup');
        if ($this->data->id == '') {
            $children = $structure->listAll($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'Layer Groups',
                'children' => $children
            ];
            $json = json_encode([$data]);
        }
        $this->renderJson($json);
    }
    
    public function formNewLayerGroup(){
        $nodeId = $this->data->id;
        if ($nodeId{0} == 'm') {
            $this->data->id = substr($this->data->id, 1);
        }
        $this->data->save = "@structure/layergroup/newLayerGroup|formNewLayerGroup";
        $this->data->close = "!$('#formNew_dialog').dialog('close');";
        $this->data->title = _M('new fnbr\models\Layer Group');
        $this->render();
    }
    
    public function formUpdateLayerGroup(){
        $model = new fnbr\models\LayerGroup($this->data->id);
        $this->data->object = $model->getData();
        $this->data->save = "@structure/layergroup/updateLayerGroup|formUpdateLayerGroup";
        $this->data->close = "!$('#formUpdate_dialog').dialog('close');";
        $this->data->title = 'Layer Group: [' . $model->getName() . ']';
        $this->render();
    }

    public function newLayerGroup()
    {
        try {
            $model = new fnbr\models\LayerGroup();
            $model->setData($this->data->layergroup);
            $model->save();
            $this->renderPrompt('information', 'OK');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }
    
    public function updateLayerGroup()
    {
        try {
            $model = new fnbr\models\LayerGroup($this->data->layergroup->idLayerGroup);
            $model->setData($this->data->layergroup);
            $model->save();
            $this->renderPrompt('information', 'OK');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

}
