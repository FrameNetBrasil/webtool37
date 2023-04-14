<?php

class CorpusController extends MController
{

    private $idLanguage;

    public function init()
    {
        Manager::checkLogin(false);
        $this->idLanguage = Manager::getSession()->idLanguage;
        $languages = \fnbr\models\Base::languages();
        $msgDir = Manager::getAppPath('conf/report');
        Manager::$msg->file = 'messages.' . $languages[$this->idLanguage] . '.php';
        Manager::$msg->addMessages($msgDir);
    }

    public function main()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        Manager::getSession()->idDomain = $this->data->idDomain;
        $this->render();
    }

    public function reportCorpus()
    {
        $this->data->idCorpus = $this->data->id;
        $corpus = new fnbr\models\Corpus($this->data->idCorpus);
        $this->data->title = $corpus->getName();
        $this->render();
    }

    public function reportDocument()
    {
        $this->data->idDocument = $this->data->id;
        $document = new fnbr\models\Document($this->data->idDocument);
        $this->data->title = $document->getName();
        $this->render();
    }

    public function reportAnnotation()
    {
        $this->data->idDocument = $this->data->id;

        $annotation = Manager::getAppService('annotation');
        $this->data->title = $annotation->getDocumentTitle($this->data->idDocument, $this->idLanguage);
        $document = new fnbr\models\Document($this->data->idDocument);
        $this->data->idSubCorpus = $document->getRelatedSubCorpus();
        if ($this->data->idSubCorpus == '') {
            $this->renderPrompt('warning', 'No SubCorpus for this Document.');
        } else {
            $this->data->title = $document->getCorpus()->getName() . ' : ' . $document->getName();
            if ((MUtil::getBooleanValue(Manager::$conf['login']['check']))) {
                $this->data->userLanguage = fnbr\models\Base::languages()[fnbr\models\Base::getCurrentUser()->getConfigData('fnbrIdLanguage')];
            } else {
                $this->data->userLanguage = fnbr\models\Base::languages()[Manager::getSession()->idLanguage];
            }
            $this->render();
        }
    }

    public function reportAnnotationDirect()
    {
        $this->data->idDocument = $this->data->id;

        $annotation = Manager::getAppService('annotation');
        $this->data->title = $annotation->getDocumentTitle($this->data->idDocument, $this->idLanguage);
        $document = new fnbr\models\Document($this->data->idDocument);
        $this->data->idSubCorpus = $document->getRelatedSubCorpus();
        if ($this->data->idSubCorpus == '') {
            $this->renderPrompt('warning', 'No SubCorpus for this Document.');
        } else {
            $this->data->title = $document->getCorpus()->getName() . ' : ' . $document->getName();
            if ((MUtil::getBooleanValue(Manager::$conf['login']['check']))) {
                $this->data->userLanguage = fnbr\models\Base::languages()[fnbr\models\Base::getCurrentUser()->getConfigData('fnbrIdLanguage')];
            } else {
                $this->data->userLanguage = fnbr\models\Base::languages()[Manager::getSession()->idLanguage];
            }
            $this->render();
        }
    }

}
