<?php

class LayerController extends MController
{

    private $idLanguage;

    public function init()
    {
        parent::init();
        $this->idLanguage = Manager::getConf('fnbr.lang');
    }

    public function formManager()
    {
        $this->data->query = Manager::getAppURL('', 'annotation/layer/gridData');
        $this->data->action = "@annotation/layer/save|formManager";
        $fnbrLayers = Manager::getSession()->fnbrLayers;
        $this->data->layersToShow = MJSON::encode($fnbrLayers ?: []);
        $this->render();
    }
    
    public function gridData()
    {
        $model = new fnbr\models\LayerType();
        $criteria = $model->listByGroup();
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }
    
    public function save() {
        $layers = $this->data->gridManager->data->checked;
        Manager::getSession()->fnbrLayers = $layers;
        $user = Manager::getLogin()->getUser();
        $user->setConfigData('fnbrLayers', $layers);
        $this->renderPrompt('information', 'OK');
    }
    

}
