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
        } elseif ($this->data->id[0] == 'f') {
            $json = $annotation->listLUs(substr($this->data->id, 1), $this->idLanguage);
        } elseif ($this->data->id[0] == 'l') {
            $json = $annotation->listSubCorpus(substr($this->data->id, 1));
        }
        $this->renderJson($json);
    }

    public function sentences()
    {
        // alterado em 17/08/2022 - dividido em sentencesLexical e sentenceCorpus
        $this->render();
    }

    public function sentencesLexical()
    {
        // alterado em 17/08/2022 - id = idLU / ignorando SubCorpus
        $annotation = Manager::getAppService('annotation');
        $this->data->idLU = $this->data->id;
        $this->data->title = $annotation->getLUTitle($this->data->idLU, $this->idLanguage);
        $this->data->userLanguage = fnbr\models\Base::languages()[fnbr\models\Base::getCurrentUser()->getConfigData('fnbrIdLanguage')];
        $this->render();
    }

    public function sentencesConstructional()
    {
        // alterado em 17/08/2022 - id = idCxn / ignorando SubCorpus
        $annotation = Manager::getAppService('annotation');
        $this->data->idCxn = substr($this->data->id, 1);
        $this->data->title = $annotation->getCxnTitle($this->data->idCxn, $this->idLanguage);
        $this->data->userLanguage = fnbr\models\Base::languages()[fnbr\models\Base::getCurrentUser()->getConfigData('fnbrIdLanguage')];
        $this->render();
    }

    public function sentencesCorpus()
    {
        // alterado em 17/08/2022 - id = idDocument
        $annotation = Manager::getAppService('annotation');
        $idDocument = substr($this->data->id, 1);
        $this->data->title = $annotation->getDocumentTitle($idDocument, $this->idLanguage);
        $this->data->idDocument = $idDocument;
        $this->data->userLanguage = fnbr\models\Base::languages()[fnbr\models\Base::getCurrentUser()->getConfigData('fnbrIdLanguage')];
        $this->render();
    }

    public function annotationSet()
    {
        // alterado em 17/08/2022 - id = idLU / ignorando SubCorpus
        // alterado em 03/02/2022 - eliminando tabela SubCorpus
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

    public function annotationSetCxn()
    {
        $annotation = Manager::getAppService('annotation');
        if ($this->data->sort) {
            $sortable = (object)[
                'field' => $this->data->sort,
                'order' => $this->data->order
            ];
        }
        $json = $annotation->listAnnotationSetCxn($this->data->id, $sortable);
        $this->renderJson($json);
    }

    public function annotationSetDocument()
    {
        $annotation = Manager::getAppService('annotation');
        if ($this->data->sort) {
            $sortable = (object)[
                'field' => $this->data->sort,
                'order' => $this->data->order
            ];
        }
        $json = $annotation->listAnnotationSetDocument($this->data->id, $sortable);
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
        $canSave = true;//($idLanguage == $userIdLanguage);
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
        //mdump($this->data);
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
        } // elseif ($this->data->id{0} == 'c') {
          //  $json = $annotation->listSubCorpusCxn(substr($this->data->id, 1));
        //}
        $this->renderJson($json);
    }

    public function headerMenu()
    {
        $annotation = Manager::getAppService('annotation');
        $json = $annotation->headerMenu($this->data->wordform);
        $this->renderJson($json);
    }

//    public function addManualSubcorpus()
//    {
//        try {
//            $annotation = Manager::getAppService('annotation');
//            $annotation->addManualSubcorpus($this->data);
//            $this->renderPrompt('info', 'OK');
//        } catch (\Exception $e) {
//            $this->renderPrompt('error', $e->getMessage());
//        }
//    }

    public function addLU()
    {
        try {
            $annotation = Manager::getAppService('annotation');
            $annotation->addLU($this->data);
            $this->renderPrompt('info', 'OK');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function addCxn()
    {
        try {
            $annotation = Manager::getAppService('annotation');
            $annotation->addCxn($this->data);
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
        } elseif ($this->data->id[0] == 'c') {
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
