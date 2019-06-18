<?php

class ExportController extends MController
{

    private $idLanguage;

    public function init()
    {
        parent::init();
        $this->idLanguage = Manager::getConf('fnbr.lang');
    }

    public function formExportFrames()
    {
        $this->data->query = Manager::getAppURL('', 'utils/export/gridDataFrames');
        $this->data->action = '@utils/export/exportFrames';
        $this->render();
    }
    
    public function gridDataFrames()
    {
        $model = new fnbr\models\Frame();
        $criteria = $model->listByFilter($this->data->filter);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }    
    
    public function exportFrames(){
        try {
            $service = Manager::getAppService('data');
            $json = $service->exportFramesToJSON($this->data->gridExportFrames->data->checked);
            $fileName = $this->data->fileName . '.json';
            $mfile = MFile::file($json, false, $fileName);
            $this->renderFile($mfile);
        } catch (EMException $e) {
            $this->renderPrompt('error',$e->getMessage());
        }
    }

    public function formExportDocWf()
    {
        $this->data->action = '@utils/export/exportDocWf';
        $this->render();
    }

    public function exportDocWf(){
        try {
            $files = \Maestro\Utils\Mutil::parseFiles('uploadFile');
            mdump($files);
            if (count($files)) {
                $service = Manager::getAppService('data');
                $mfile = $service->parseDocWf($files[0]);
                $this->renderFile($mfile);
            } else {
                $this->renderPrompt('information','OK');
            }
        } catch (EMException $e) {
            $this->renderPrompt('error',$e->getMessage());
        }

    }

    public function formExportCxnFS()
    {
        $this->data->action = '@utils/export/exportCxnFS';
        $this->render();
    }

    public function exportCxnFS(){
        try {
            $this->data->idLanguage = Manager::getSession()->idLanguage;
            $service = Manager::getAppService('data');
            $fs =  $service->exportCxnToFS($this->data);
            $fileName = $this->data->fileName . '.json';
            $mfile = \MFile::file($fs, false, $fileName);
            $this->renderFile($mfile);
        } catch (EMException $e) {
            $this->renderPrompt('error',$e->getMessage());
        }
    }

    public function formExportCxn()
    {
        $this->data->query = Manager::getAppURL('', 'utils/export/gridDataCxn');
        $this->data->action = '@utils/export/exportCxn';
        $this->render();
    }

    public function gridDataCxn()
    {
        $model = new fnbr\models\Construction();
        $criteria = $model->listByFilter($this->data->filter);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }

    public function exportCxn(){
        try {
            $service = Manager::getAppService('data');
            $json = $service->exportCxnToJSON($this->data->gridExportCxn->data->checked);
            $fileName = $this->data->fileName . '.json';
            $mfile = MFile::file($json, false, $fileName);
            $this->renderFile($mfile);
        } catch (EMException $e) {
            $this->renderPrompt('error',$e->getMessage());
        }
    }

    public function exportCONLL(){
        try {
            $idDocument = $this->data->id;
            $document = new fnbr\models\Document($idDocument);
            $service = Manager::getAppService('data');
            $conll = $service->exportDocumentToCONLL($document);
            $fileName = $document->getName() . '.conll.txt';
            $mfile = MFile::file($conll, false, $fileName);
            $this->renderFile($mfile);
        } catch (EMException $e) {
            $this->renderPrompt('error',$e->getMessage());
        }
    }

    public function formExportXML(){
        try {
            $this->data->idCorpus = $this->data->id;
            $corpus = new fnbr\models\Corpus($this->data->idCorpus);
            $user = fnbr\models\Base::getCurrentUser();
            $this->data->corpusName = $corpus->getName();
            $this->data->email = $user->getEmail();
            $this->data->action = '@utils/export/exportXML';
            $this->render();
        } catch (EMException $e) {
            $this->renderPrompt('error',$e->getMessage());
        }
    }

    public function exportXML(){
        try {
            $corpus = new fnbr\models\Corpus($this->data->idCorpus);
            $this->data->idLanguage = Manager::getSession()->idLanguage;
            $user = fnbr\models\Base::getCurrentUser();
            $documents = "";
            foreach($this->data->documents as $idDocument) {
                $documents .= ':' . $idDocument;
            }
            $timeWrapper = realpath(Manager::getAppPath() . "/offline/timewrapper.php");
            $offline = '"' . addslashes(realpath(Manager::getAppPath() . "/offline/exportCorpusXML.php")) . '" ' . "{$corpus->getEntry()} {$documents} {$this->data->idLanguage} {$user->getIdUser()} {$user->getEmail()}";
            if (substr(php_uname(), 0, 7) == "Windows") {
                try {
                    $commandString = "start /b php.exe {$timeWrapper} " . $offline;
                    pclose(popen($commandString, "r"));
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            } else {
                mdump("php {$offline} > /dev/null &");
                exec("php {$offline} > /dev/null &");
            }
            $this->renderPrompt('information',"OK. XML file will be generated. A notification will be sent to {$user->getEmail()}.");

        } catch (Exception $e) {
            $this->renderPrompt('error',$e->getMessage());
        }
    }


}
