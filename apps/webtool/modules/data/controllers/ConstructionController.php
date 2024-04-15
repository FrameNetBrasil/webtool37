<?php

class ConstructionController extends MController
{

    public function lookupData()
    {
        $model = new fnbr\models\Construction();
        $criteria = $model->listForLookupName($this->data->q);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }

    public function lookupDataInhCE()
    {
        $model = new fnbr\models\ConstructionElement();
        $query = $model->listForLookupInhName($this->data->idConstructionElement, $this->data->q);
        $this->renderJSON($model->gridDataAsJSON($query));

    }

}