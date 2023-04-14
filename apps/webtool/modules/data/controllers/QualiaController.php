<?php

class QualiaController extends MController
{

    public function lookupData()
    {
        $type = $this->data->id;
        $idLanguage = Manager::getSession()->idLanguage;
        $model = new fnbr\models\Qualia();
        $criteria = $model->listForLookup($type, $idLanguage);
        $json = $model->gridDataAsJSON($criteria);
        $this->renderJSON($json);
    }

    public function lookupQualiaRelation()
    {
        $qualiaType = $this->data->id;
        $idLanguage = Manager::getSession()->idLanguage;
        $model = new fnbr\models\Qualia();
        $criteria = $model->listRelationForLookup($qualiaType, $idLanguage);
        $json = $model->gridDataAsJSON($criteria);
        $this->renderJSON($json);
    }

    public function lookupForGrid()
    {
        $idLanguage = Manager::getSession()->idLanguage;
        $model = new fnbr\models\Qualia();
        $criteria = $model->listForGrid($this->data, $idLanguage);
        $json = $model->gridDataAsJSON($criteria, true);
        $this->renderJSON($json);
    }

    public function lookupRelationForGrid()
    {
        $idLanguage = Manager::getSession()->idLanguage;
        $model = new fnbr\models\Qualia();
        $criteria = $model->listRelationForGrid($this->data, $idLanguage);
        $json = $model->gridDataAsJSON($criteria);
        $this->renderJSON($json);
    }

}