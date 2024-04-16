<?php

class StaticController extends MController
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

    public function formImageAnnotation()
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
    public function multimodalImageTree()
    {
        $annotation = Manager::getAppService('annotation');
        if ($this->data->id == '') {
            $json = $annotation->listCorpusMultimodalImage($this->data->corpus, $this->idLanguage);
        } elseif ($this->data->id[0] == 'c') {
            $json = $annotation->listCorpusDocumentMultimodal(substr($this->data->id, 1));
        }
        $this->renderJson($json);
    }

    public function sentencesFlickr30k()
    {
        $type = $this->data->id[0];
        if ($type == 'd') {
            $document = new fnbr\models\Document();
            $this->data->idDocument = (int)substr($this->data->id, 1);
            $document->getById($this->data->idDocument);
            $documentEntry = $document->getEntryObject();
            $this->data->documentName = $documentEntry->name;
            $documentmm = new fnbr\models\DocumentMM();
            $documentmm-> getByIdDocument($this->data->idDocument);
            $this->data->flickr30k = $documentmm->getFlickr30k();
            $this->render();
        }
    }

    public function imageSentenceMultimodal() {
        $idDocument = $this->data->id;
        $annotation = Manager::getAppService('annotation');
        $json = $annotation->listImageSentence($idDocument);
        $this->renderJson($json);
    }

    public function annotationFlickr30k() {
        Manager::getPage()->setTemplateName('content');
        $idStaticSentenceMM = $this->data->id;
        $staticSentenceMM = new fnbr\models\StaticSentenceMM($idStaticSentenceMM);
        $sentence = new fnbr\models\Sentence($staticSentenceMM->getIdSentence());
        $imageMM = new fnbr\models\ImageMM($staticSentenceMM->getIdImageMM());
        $document = $staticSentenceMM->getDocument();
        $documentMM = new fnbr\models\DocumentMM();
        $documentMM->getByIdDocument($document->getIdDocument());

        $this->data->staticSentenceMM = $staticSentenceMM->getData();
        $this->data->sentence = $sentence->getData();
        $this->data->imageMM = $imageMM->getData();
        $this->data->document = $document->getData();
        $this->data->document->name = $document->getName();
        $this->data->documentMM = $documentMM->getData();

        //$this->data->objects = $imageMM->getObjects();
        $this->data->objects = $staticSentenceMM->getObjectsForAnnotationImage();
        $this->data->urlLookupFrame = Manager::getBaseURL() . '/index.php/webtool/data/frame/lookupData';
        $this->data->urlLookupFE =  Manager::getBaseURL() . '/index.php/webtool/data/frameelement/lookupDataDecorated';
        $this->render();
    }

    public function getImageAnnotation() {
        try {
            $staticObjectSentenceMM = new \fnbr\models\StaticObjectSentenceMM();
            $data = $staticObjectSentenceMM->getAnnotation($this->data->idStaticSentenceMM);
            $this->renderJSon(json_encode(['type' => 'success', 'message' => 'Ok', 'data' => $data]));
        } catch (\Exception $e) {
            $this->renderJSon(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        }
    }

    public function lemmaLoader() {
        try {
            $lemma = new \fnbr\models\Lemma();
            $result = $lemma->listForLookup($this->data->q, 2)->asQuery()->getResult();
            $this->renderJSon(json_encode(['type' => 'success', 'data' => $result]));
        } catch (\Exception $e) {
            $this->renderJSon(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        }
    }

    public function updateImageObject() {
        try {
            $message = ($this->data->idFrame ? 'Entity updated.' : 'Entity removed.');
            $objectSentenceMM = new \fnbr\models\StaticObjectSentenceMM();
            $objectSentenceMM->updateObject($this->data);
            $result = $objectSentenceMM->getData();
            $result->message = $message;
            $this->renderJSon(json_encode(['type' => 'success', 'message' => $message, 'data' => $result]));
        } catch (\Exception $e) {
            $this->renderJSon(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        }
    }

    public function updateImageAnnotation() {
        try {
            $objectSentenceMM = new \fnbr\models\StaticObjectSentenceMM();
            $objectSentenceMM->updateAnnotation($this->data);
            $this->renderJSon(json_encode(['type' => 'success', 'message' => 'Annotation saved.']));
        } catch (\Exception $e) {
            $this->renderJSon(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        }
    }

    /*
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

    public function sentencesMultimodal()
    {
        $annotation = Manager::getAppService('annotation');
        $type = $this->data->id[0];
        if ($type == 'd') {
            $this->data->idDocument = substr($this->data->id, 1);
            $documentMM = new fnbr\models\DocumentMM();
            $documentMM->getByIdDocument($this->data->idDocument);
            $this->data->documentMM = $documentMM->getData();
            $this->render();
        }
    }

    public function annotationSetMultimodal()
    {
        $multimodal = Manager::getAppService('multimodal');
        $idDocumentMM = $this->data->id;
        $json = $multimodal->listAnnotationSetMultimodal($idDocumentMM);
        $this->renderJson($json);
    }

    public function annotationVideo() {
        Manager::getPage()->setTemplateName('content');
        $this->data->idDocumentMM = $this->data->id;
        //$this->data->urlObjects = Manager::getURL('annotation/multimodal/objectsData') . "/" . $this->data->idSentenceMM;
        //$this->data->urlPutObjects = Manager::getURL('annotation/multimodal/putObjects');
        //$sentenceMM = new fnbr\models\SentenceMM($this->data->idSentenceMM);
        //$this->data->idSentence = $sentenceMM->getIdSentence();
        //$this->data->sentenceMMRangeTime = $sentenceMM->getStartTimeStamp() . ' - ' . $sentenceMM->getEndTimeStamp();
        $documentMM = new fnbr\models\DocumentMM($this->data->idDocumentMM);
        $document = new fnbr\models\Document($documentMM->getIdDocument());
        $this->data->documentMM = $documentMM->getData();
        $this->data->objects = $documentMM->getObjects();
        $this->data->swfPath = Manager::getBaseURL() . '/apps/webtool/public/scripts/jplayer/';
        $this->data->urlLookupFrame = Manager::getBaseURL() . '/index.php/webtool/data/frame/lookupData';
        $this->data->urlLookupFE =  Manager::getBaseURL() . '/index.php/webtool/data/frameelement/lookupDataDecorated';
        $this->render();
    }

    public function annotationVideoWindows() {
        Manager::getPage()->setTemplateName('content');
        $this->data->idDocumentMM = $this->data->id;
        //$this->data->urlObjects = Manager::getURL('annotation/multimodal/objectsData') . "/" . $this->data->idSentenceMM;
        //$this->data->urlPutObjects = Manager::getURL('annotation/multimodal/putObjects');
        //$sentenceMM = new fnbr\models\SentenceMM($this->data->idSentenceMM);
        //$this->data->idSentence = $sentenceMM->getIdSentence();
        //$this->data->sentenceMMRangeTime = $sentenceMM->getStartTimeStamp() . ' - ' . $sentenceMM->getEndTimeStamp();
        $documentMM = new fnbr\models\DocumentMM($this->data->idDocumentMM);
        $document = new fnbr\models\Document($documentMM->getIdDocument());
        $this->data->documentMM = $documentMM->getData();
        $this->data->objects = $documentMM->getObjects();
        $this->data->swfPath = Manager::getBaseURL() . '/apps/webtool/public/scripts/jplayer/';
        $this->data->urlLookupFrame = Manager::getBaseURL() . '/index.php/webtool/data/frame/lookupData';
        $this->data->urlLookupFE =  Manager::getBaseURL() . '/index.php/webtool/data/frameelement/lookupDataDecorated';
        $this->render();
    }

    public function loadObjects() {
        $this->data->idDocumentMM = $this->data->id;
        $documentMM = new fnbr\models\DocumentMM($this->data->idDocumentMM);
        $objects = $documentMM->getObjects();
        $this->renderJSON(json_encode($objects));
    }

    public function renderVideo() {
        Manager::getPage()->setTemplateName('content');
        try {
            $user = fnbr\models\Base::getCurrentUser();
            $multimodalService = Manager::getAppService('multimodal');
            $multimodalService->renderVideo($this->data->id);
            $this->render();
        } catch (\Exception $e) {
            $this->renderPrompt('error', "Error preprocessing file. " . $e->getMessage());
        }
    }

    public function updateObject() {
        mdump($this->data);
        try {
            $objectMM = new \fnbr\models\ObjectMM();
            $objectMM->updateObject($this->data);
            $result = $objectMM->getData();
            $this->renderJSon(json_encode(['type' => 'success', 'message' => 'Object saved.', 'data' => $result]));
        } catch (\Exception $e) {
            $this->renderJSon(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        }
    }

    public function deleteObjects() {
        mdump($this->data);
        try {
            $objectMM = new \fnbr\models\ObjectMM();
            $objectMM->deleteObjects($this->data->toDelete);
            $this->renderJSon(json_encode(['type' => 'success', 'message' => 'Objects deleted.']));
        } catch (\Exception $e) {
            $this->renderJSon(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        }
    }


    public function multimodalImageTree()
    {
        $annotation = Manager::getAppService('annotation');
        if ($this->data->id == '') {
            $json = $annotation->listCorpusMultimodalImage($this->data->corpus, $this->idLanguage);
        } elseif ($this->data->id[0] == 'c') {
            $json = $annotation->listCorpusDocumentMultimodal(substr($this->data->id, 1));
        }
        $this->renderJson($json);
    }

    public function sentencesImageMultimodal()
    {
        $type = $this->data->id[0];
        if ($type == 'd') {
//            $this->data->idDocument = substr($this->data->id, 1);
//            $documentMM = new fnbr\models\DocumentMM();
//            $documentMM->getByIdDocument($this->data->idDocument);
//            $this->data->documentMM = $documentMM->getData();
//            $this->render();
            $documentMM = new fnbr\models\DocumentMM();
            $this->data->idDocumentMM = substr($this->data->id, 1);
            $documentMM->getById($this->data->idDocumentMM);
            $this->data->documentMM = $documentMM->getData();
            $this->render();
        }
    }


    public function imageSentenceMultimodal() {
        $idDocumentMM = $this->data->id;
        $annotation = Manager::getAppService('annotation');
        $json = $annotation->listImageSentence($idDocumentMM);
        $this->renderJson($json);
    }

    public function annotationImageSentence() {
        Manager::getPage()->setTemplateName('content');
        $idSentenceMM = $this->data->id;
        $sentenceMM = new fnbr\models\SentenceMM($idSentenceMM);
        $sentence = new fnbr\models\Sentence($sentenceMM->getIdSentence());
        $imageMM = new fnbr\models\ImageMM($sentenceMM->getIdImageMM());
        $document = $sentence->getParagraph()->getDocument();

        $this->data->sentenceMM = $sentenceMM->getData();
        $this->data->sentence = $sentence->getData();
        $this->data->imageMM = $imageMM->getData();
        $this->data->document = $document->getData();
        $this->data->document->name = $document->getName();

        //$this->data->objects = $imageMM->getObjects();
        //$this->data->sentenceObjects = $sentenceMM->getSentenceObjects();
        $this->data->objects = $sentenceMM->getObjectsForAnnotationImage();
        $this->data->urlLookupFrame = Manager::getBaseURL() . '/index.php/webtool/data/frame/lookupData';
        $this->data->urlLookupFE =  Manager::getBaseURL() . '/index.php/webtool/data/frameelement/lookupDataDecorated';
        $this->data->urlLookupLUs = Manager::getBaseURL() . '/index.php/webtool/data/lu/lookupDataImageAnnotation';
        $this->render();
    }



    public function deleteImageAnnotation() {
        try {
            $objectSentenceMM = new \fnbr\models\ObjectSentenceMM();
            $objectSentenceMM->deleteAnnotation($this->data->toDelete);
            $this->renderJSon(json_encode(['type' => 'success', 'message' => 'Annotation(s) deleted.']));
        } catch (\Exception $e) {
            $this->renderJSon(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        }
    }



    public function deleteObjectFrame() {
        try {
            $objectMM = new \fnbr\models\ObjectMM();
            $objectMM->deleteObjectFrame($this->data->idObjectFrameMM);
            $this->renderJSon(json_encode(['type' => 'success', 'message' => 'Object saved.']));
        } catch (\Exception $e) {
            $this->renderJSon(json_encode(['type' => 'error', 'message' => $e->getMessage()]));
        }
    }

    */

}
