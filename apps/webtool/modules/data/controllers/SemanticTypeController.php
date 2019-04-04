<?php

class SemanticTypeController extends MController {

    public function lookupData($rowsOnly = false, $idDomain = 0){
        $model = new fnbr\models\SemanticType();
        $filter = (object) ['idDomain' => $idDomain, 'name' => $this->data->q];
        $criteria = $model->listForLookup($filter);
        $this->renderJSON($model->gridDataAsJSON($criteria, $rowsOnly));
    }

    public function lookupDataForLU($rowsOnly = false){
        $model = new fnbr\models\SemanticType();
        $query = $model->listForLookupLU();
        $this->renderJSON($model->gridDataAsJSON($query, $rowsOnly));
    }
}