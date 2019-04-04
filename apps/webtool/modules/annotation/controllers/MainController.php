<?php

class MainController extends MController
{

    private $idLanguage;

    public function init()
    {
        parent::init();
        $this->idLanguage = \Manager::getSession()->idLanguage;
    }

    public function main()
    {
        $this->render();
    }

    public function formLexicalAnnotation()
    {
        $annotation = Manager::getAppService('annotation');
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $this->data->isSenior = Manager::checkAccess('SENIOR', A_EXECUTE) ? 'true' : 'false';
        $this->data->colors = $annotation->getColor();
        $this->data->layerType = $annotation->getLayerType();
        $it = $annotation->getInstantiationType();
        $this->data->instantiationType = $it['array'];
        $this->data->instantiationTypeObj = $it['obj'];
        $this->render();
    }

    public function frameTree()
    {
        $annotation = Manager::getAppService('annotation');
        if ($this->data->id == '') {
            $json = $annotation->listFrames($this->data->lu, $this->idLanguage);
        } elseif ($this->data->id{0} == 'f') {
            $json = $annotation->listLUs(substr($this->data->id, 1), $this->idLanguage);
        } elseif ($this->data->id{0} == 'l') {
            $json = $annotation->listSubCorpus(substr($this->data->id, 1));
        }
        $this->renderJson($json);
    }

    public function sentences()
    {
        $annotation = Manager::getAppService('annotation');
        $type = $this->data->id{0};
        if ($type == 'd') {
            $idDocument = substr($this->data->id, 1);
            $this->data->title = $annotation->getDocumentTitle($idDocument, $this->idLanguage);
            $document = new fnbr\models\Document($idDocument);
            $this->data->idSubCorpus = $document->getRelatedSubCorpus();
        } else {
            $this->data->idSubCorpus = $this->data->id;
        }
        if ($this->data->idSubCorpus == '') {
            $this->renderPrompt('warning', 'No SubCorpus for this Document.');
        } else {
            $this->data->status = $annotation->getSubCorpusStatus($this->data->idSubCorpus, $this->data->cxn);
            foreach ($this->data->status->stat as $stat) {
                $stats .= "({$stat->name}: {$stat->quant})  ";
            }
            $this->data->title = $annotation->getSubCorpusTitle($this->data->idSubCorpus, $this->idLanguage, $this->data->cxn) . "  - Stats: {$stats}  -  Status: {$this->data->status->status->msg}";
            $this->data->userLanguage = fnbr\models\Base::languages()[fnbr\models\Base::getCurrentUser()->getConfigData('fnbrIdLanguage')];
            $this->render();
        }
    }

    public function annotationSet()
    {
        $annotation = Manager::getAppService('annotation');
        if ($this->data->sort) {
            $sortable = (object)[
                'field' => $this->data->sort,
                'order' => $this->data->order
            ];
        }
        $json = $annotation->listAnnotationSet($this->data->id, $sortable);
        $this->renderJson($json);
    }

    public function annotation()
    {
        $this->data->idSentence = $this->data->id;
        $this->data->idAnnotationSet = Manager::getContext()->get(1);
        $this->data->type = Manager::getContext()->get(2);
        $this->render();
    }

    public function layers()
    {
        $annotation = Manager::getAppService('annotation');
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $this->data->isSenior = Manager::checkAccess('SENIOR', A_EXECUTE) ? 'true' : 'false';
        $this->data->sessionTimeout = Manager::getConf('session.timeout');
        $this->data->colors = $annotation->getColor();
        $this->data->layerType = $annotation->getLayerType();
        $it = $annotation->getInstantiationType();
        $this->data->instantiationType = $it['array'];
        $this->data->instantiationTypeObj = $it['obj'];
        $this->data->idSentence = $this->data->id;
        $sentence = new fnbr\models\Sentence($this->data->idSentence);
        $idLanguage = $sentence->getIdLanguage();
        $userIdLanguage = fnbr\models\Base::getCurrentUser()->getConfigData('fnbrIdLanguage');
        $canSave = ($idLanguage == $userIdLanguage);
        $this->data->canSave = $canSave && Manager::checkAccess('BEGINNER', A_EXECUTE);
        $this->data->idAnnotationSet = Manager::getContext()->get(1);
        $this->data->type = Manager::getContext()->get(2);
        //mdump($this->data);
        $annotation = Manager::getAppService('annotation');
        $this->data->layers = $annotation->getLayers($this->data, $this->idLanguage);
        $this->render();
    }

    public function layersData()
    {
        $this->data->idSentence = $this->data->id;
        $this->data->idAnnotationSet = Manager::getContext()->get(1);
        $this->data->type = Manager::getContext()->get(2);
        $annotation = Manager::getAppService('annotation');
        mdump($this->data);
        $this->data->layersData = $annotation->getLayersData($this->data, $this->idLanguage);
        $this->renderJson($this->data->layersData);
    }

    public function validation()
    {
        try {
            $annotation = Manager::getAppService('annotation');
            $as = json_decode($this->data->annotationSets);
            $annotation->validation($as, $this->data->validation, $this->data->feedback);
            $this->renderPrompt('information', 'ok', "!annotation.showSubCorpus(annotation.idSubCorpus)");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function notifySupervisor()
    {
        try {
            $annotation = Manager::getAppService('annotation');
            $as = json_decode($this->data->asForSupervisor);
            $annotation->notifySupervisor($as);
            $this->renderPrompt('information', 'ok');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function putLayers()
    {
        try {
            $this->data->sessionTimeout = Manager::getConf('session.timeout');
            $annotation = Manager::getAppService('annotation');
            $layers = json_decode($this->data->dataLayers);
            $annotation->putLayers($layers);
            $action = ($this->data->type == 'l' ? "!annotation.showSubCorpus(annotation.idSubCorpus)" : '');
            //$this->renderPrompt('information', 'ok', $action);
            $this->render();
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function addFELayer()
    {
        $annotation = Manager::getAppService('annotation');
        $layer = $annotation->addFELayer($this->data->idAnnotationSet);
        $this->renderJSON(json_encode($layer));
    }

    public function getFELabels()
    {
        $annotation = Manager::getAppService('annotation');
        $labels = $annotation->getFELabels($this->data->idAnnotationSet, $this->data->idSentence);
        $this->renderJSON(json_encode($labels));
    }

    public function delFELayer()
    {
        $annotation = Manager::getAppService('annotation');
        $annotation->delFELayer($this->data->idAnnotationSet);
        $this->render();
    }

    public function formConstructionalAnnotation()
    {
        $annotation = Manager::getAppService('annotation');
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $this->data->isSenior = Manager::checkAccess('SENIOR', A_EXECUTE) ? 'true' : 'false';
        $this->data->colors = $annotation->getColor();
        $this->data->layerType = $annotation->getLayerType();
        $it = $annotation->getInstantiationType();
        $this->data->instantiationType = $it['array'];
        $this->data->instantiationTypeObj = $it['obj'];
        $this->render();
    }

    public function cxnTree()
    {
        $annotation = Manager::getAppService('annotation');
        if ($this->data->id == '') {
            $json = $annotation->listCxn($this->data->cxn, $this->idLanguage);
        } elseif ($this->data->id{0} == 'c') {
            $json = $annotation->listSubCorpusCxn(substr($this->data->id, 1));
        }
        $this->renderJson($json);
    }

    public function headerMenu()
    {
        $annotation = Manager::getAppService('annotation');
        $json = $annotation->headerMenu($this->data->wordform);
        $this->renderJson($json);
    }

    public function addManualSubcorpus()
    {
        try {
            $annotation = Manager::getAppService('annotation');
            $annotation->addManualSubcorpus($this->data);
            $this->renderPrompt('info', 'OK');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function cxnGridData()
    {
        $annotation = Manager::getAppService('annotation');
        $data = $annotation->cxnGridData();
        $this->renderJSON($data);
    }

    public function formCorpusAnnotation()
    {
        $annotation = Manager::getAppService('annotation');
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $this->data->isSenior = Manager::checkAccess('SENIOR', A_EXECUTE) ? 'true' : 'false';
        $this->data->colors = $annotation->getColor();
        $this->data->layerType = $annotation->getLayerType();
        $it = $annotation->getInstantiationType();
        $this->data->instantiationType = $it['array'];
        $this->data->instantiationTypeObj = $it['obj'];
        $this->render();
    }

    public function corpusTree()
    {
        $annotation = Manager::getAppService('annotation');
        if ($this->data->id == '') {
            $json = $annotation->listCorpus($this->data->corpus, $this->idLanguage);
        } elseif ($this->data->id{0} == 'c') {
            $json = $annotation->listCorpusDocument(substr($this->data->id, 1));
        }
        $this->renderJson($json);
    }

    public function changeStatusAS()
    {
        try {
            $annotation = Manager::getAppService('annotation');
            $as = json_decode($this->data->asToChange);
            $annotation->changeStatusAS($as, $this->data->asNewStatus);
            $this->renderPrompt('information', 'ok', "!annotation.showSubCorpus(annotation.idSubCorpus)");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function deleteAS()
    {
        try {
            $annotation = Manager::getAppService('annotation');
            $annotation->deleteAS($this->data->AStoDelete);
            $this->renderPrompt('information', 'ok');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function labelHelp()
    {
        $annotation = Manager::getAppService('annotation');
        $this->data->labels = $annotation->getLabelHelp($this->idLanguage);
        $this->render();
    }

    public function formASComments()
    {
        $annotation = Manager::getAppService('annotation');
        $this->data->object->asc = $annotation->getASComments($this->data->id);
        $this->render();
    }

    public function saveASComments()
    {
        try {
            $annotation = Manager::getAppService('annotation');
            $annotation->saveASComments($this->data->asc);
            $this->renderPrompt('information', 'ok');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

}
