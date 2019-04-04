<?php

class DomainController extends MController
{

    public function main()
    {
        $this->render("formBase");
    }

    public function lookupData($rowsOnly)
    {
        $model = new fnbr\models\Domain();
        $criteria = $model->listAll();
        $this->renderJSON($model->gridDataAsJSON($criteria, $rowsOnly));
    }

    public function saveFrameDomain()
    {
        try {
            $structure = Manager::getAppService('structuredomain');
            $structure->saveFrameDomain($this->data->idFrame, $this->data->toSave);
            $this->renderPrompt('information', "Ok", "$('#{$this->data->idGrid}').datagrid('reload');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function saveCxnDomain()
    {
        try {
            $structure = Manager::getAppService('structuredomain');
            $structure->saveConstructionDomain($this->data->idConstruction, $this->data->toSave);
            $this->renderPrompt('information', "Ok", "$('#{$this->data->idGrid}').datagrid('reload');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }


}