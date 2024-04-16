<?php

use fnbr\models\DynamicObjectMM;

class DynamicController extends MController
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

    public function formVideoAnnotation()
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

    public function multimodalTree()
    {
        $annotation = Manager::getAppService('annotation');
        if ($this->data->id == '') {
            $json = $annotation->listCorpusMultimodal($this->data->corpus, $this->idLanguage);
        } elseif ($this->data->id[0] == 'c') {
            $json = $annotation->listCorpusDocumentMultimodal(substr($this->data->id, 1));
        }
        $this->renderJson($json);
    }
    public function annotation() {
        Manager::getPage()->setTemplateName('content');
        $this->data->idDocument = $this->data->id;
        $document = new fnbr\models\Document($this->data->idDocument);
        $documentMM = new fnbr\models\DocumentMM();
        $documentMM->getByIdDocument($document->getId());
        $this->data->documentMM = $documentMM->getData();
        $dynamicObjectMM = new DynamicObjectMM();
        $this->data->objects = [];//$dynamicObjectMM->getObjectsByDocument($document->getId());
        $this->data->swfPath = Manager::getBaseURL() . '/apps/webtool/public/scripts/jplayer/';
//        $document = new fnbr\models\Document($this->data->documentMM->idDocument);
        $this->data->document = $document->getEntryObject();
        $corpus = new fnbr\models\Corpus($document->getIdCorpus());
        $this->data->corpus = $corpus->getEntryObject();
//        $this->data->urlLookupFrame = Manager::getBaseURL() . '/index.php/webtool/data/frame/lookupData';
//        $this->data->urlLookupFE =  Manager::getBaseURL() . '/index.php/webtool/data/frameelement/lookupDataDecorated';
        $this->render();
    }
    public function loadObjects() {
        $this->data->idDocumentMM = $this->data->id;
        $documentMM = new fnbr\models\DocumentMM($this->data->idDocumentMM);
        $document = new fnbr\models\Document($documentMM->getIdDocument());
        $dynamicObjectMM = new DynamicObjectMM();
        $objects = $dynamicObjectMM->getObjectsByDocument($document->getId());
        //$objects = $documentMM->getObjects();
        $this->renderJSON(json_encode($objects));
    }

    public function sentences()
    {
        $multimodal = Manager::getAppService('multimodal');
        $idDocumentMM = $this->data->id;
        $json = $multimodal->listSentencesForDynamic($idDocumentMM);
        $this->renderJson($json);
    }


    public function updateObject() {
        mdump($this->data);
        try {
            $objectMM = new \fnbr\models\DynamicObjectMM();
            $objectMM->updateObject($this->data);
            $result = $objectMM->getData();
            $result->idObjectMM = $result->idDynamicObjectMM;
            $this->renderJSon(json_encode(['type' => 'success', 'message' => 'Object saved.', 'data' => $result]));
        } catch (\Exception $e) {
            $this->renderJSon(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        }
    }

    public function updateObjectData() {
        mdump($this->data);
        try {
            $objectMM = new \fnbr\models\DynamicObjectMM();
            $objectMM->updateObjectData($this->data);
            $result = $objectMM->getData();
            $result->idObjectMM = $result->idDynamicObjectMM;
            $this->renderJSon(json_encode(['type' => 'success', 'message' => 'Object saved.', 'data' => $result]));
        } catch (\Exception $e) {
            $this->renderJSon(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        }
    }

    public function deleteObjects() {
        mdump($this->data);
        try {
            $objectMM = new \fnbr\models\DynamicObjectMM();
            $objectMM->deleteObjects($this->data->toDelete);
            $this->renderJSon(json_encode(['type' => 'success', 'message' => 'Objects deleted.']));
        } catch (\Exception $e) {
            $this->renderJSon(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        }
    }

}
