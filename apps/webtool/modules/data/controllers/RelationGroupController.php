<?php

class RelationGroupController extends MController
{

    public function lookupData($rowsOnly)
    {
        $model = new fnbr\models\RelationGroup();
        $criteria = $model->listAll();
        $this->renderJSON($model->gridDataAsJSON($criteria, $rowsOnly));
    }


}