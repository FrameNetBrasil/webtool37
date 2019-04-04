<?php

class ConstructionController extends MController {

    public function lookupData(){
        $model = new fnbr\models\Construction();
        $criteria = $model->listForLookupName($this->data->q);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }

}