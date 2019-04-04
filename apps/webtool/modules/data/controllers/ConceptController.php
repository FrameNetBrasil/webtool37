<?php

class ConceptController extends MController {

    public function lookupData($rowsOnly = false, $idDomain = 0){
        $model = new fnbr\models\Concept();
        $filter = (object) ['name' => $this->data->q];
        $criteria = $model->listForLookup($filter);
        $this->renderJSON($model->gridDataAsJSON($criteria, $rowsOnly));
    }

}