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

}
