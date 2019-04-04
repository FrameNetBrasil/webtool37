<?php

class LanguageController extends MController
{

    public function main()
    {
        $this->data->query = Manager::getAppURL('', 'language/gridData');
        $this->render();
    }

    public function gridData()
    {
        $model = new fnbr\models\Language($this->data->id);
        $criteria = $model->listByFilter($this->data->filter);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }

    public function comboData()
    {
        $model = new fnbr\models\Language($this->data->id);
        $criteria = $model->listForCombo();
        $this->renderJSON($model->gridDataAsJSON($criteria, true));
    }

}
