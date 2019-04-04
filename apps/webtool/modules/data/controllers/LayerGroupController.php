<?php

class LayerGroupController extends MController
{

    public function lookupData($rowsOnly)
    {
        $model = new fnbr\models\LayerGroup();
        $criteria = $model->listAll();
        $this->renderJSON($model->gridDataAsJSON($criteria, $rowsOnly));
    }


}