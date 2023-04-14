<?php

class LemmaController extends MController
{

    public function lookupData()
    {
        $json = json_encode([]);
        if (strlen($this->data->q) > 2) {
            $idLanguage = \Manager::getSession()->idLanguage;
            $model = new fnbr\models\Lemma();
            $criteria = $model->listForLookup($this->data->q, $idLanguage);
            $json = $model->gridDataAsJSON($criteria);
        }
        $this->renderJSON($json);
    }
}