<?php

class WordFormController extends MController {

    public function lookupData(){
        if (strlen($this->data->q) < 3) {
            $json = json_encode([]);
        } else {
            $model = new fnbr\models\WordForm();
            $criteria = $model->listForLookup($this->data->q);
            $json = $model->gridDataAsJSON($criteria);
        }
        $this->renderJSON($json);
    }


}