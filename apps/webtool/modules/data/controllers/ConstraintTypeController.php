<?php

class ConstraintTypeController extends MController
{

    public function getById()
    {
        $model = new fnbr\models\ConstraintType();
        $this->data->idConstraintType = $this->data->id;
        $result = $model->listByFilter($this->data)->asQuery()->getResult();
        $data = $result[0];
        $this->renderJSON(json_encode($result[0]));
    }

}