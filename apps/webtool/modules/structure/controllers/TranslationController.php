<?php
/**
 * $_comment
 *
 * @category   Maestro
 * @package    UFJF
 * @subpackage $_package
 * @copyright  Copyright (c) 2003-2012 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version    
 * @since      
 */



class TranslationController extends MController {

    public function main()
    {
        $this->data->query = Manager::getAppURL('', 'translation/gridData');
        $this->render();
    }

    public function gridData()
    {
        $model = new fnbr\models\Transalation($this->data->id);
        $criteria = $model->listByFilter($this->data->filter);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }

    public function formObject()
    {
        $model = new fnbr\models\Entry($this->data->id);
        $this->data->forUpdate = ($this->data->id != '');
        $this->data->object = $model->getData();
        $this->data->title = $this->data->forUpdate ? $model->getDescription() : _M("new fnbr\models\Entry");
        $this->data->save = "@entry/save/" . $model->getId() . '|formObject';
        $this->data->delete = "@entry/delete/" . $model->getId() . '|formObject';
        $this->render();
    }

    public function formUpdate()
    {
        $this->data->title = "Translation: " . $this->data->id;
        $this->data->query = Manager::getAppURL('', 'translation/gridUpdateData/' . $this->data->id);
        $this->render();
    }

    public function gridUpdateData()
    {
        $model = new fnbr\models\Translation();
        $filter = (object)[
            'resource' => $this->data->id
        ];
        $criteria = $model->listForUpdate($filter);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }
    
    public function formUpdateTranslation()
    {
        $model = new fnbr\models\Translation($this->data->id);
        $this->data->object = $model->getData();
        $this->data->title = $model->getResource();
        $this->data->language = $model->getLanguage()->getLanguage();
        $this->data->close = "!$('#formUpdateTranslation_dialog').dialog('close');";        
        $this->data->save = "@translation/save/" . $model->getId() . '|formUpdateTranslation';
        $this->render();
    }

    public function save()
    {
        try {
            $model = new fnbr\models\Translation($this->data->id);
            $model->setData($this->data->translation);
            $model->save();
            $this->renderPrompt('information', 'OK');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $model = new fnbr\models\Translation($this->data->id);
            $model->delete();
            $go = "!$('#formObject_dialog').dialog('close');";
            $this->renderPrompt('information', _M("Record [%s] removed.", $model->getDescription()), $go);
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }


}