<?php

class TemplateController extends MController {

    public function lookupData(){
        $model = new fnbr\models\Template();
        $criteria = $model->listForLookup($this->data->id);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }
/*

    public function main() {
        $this->render("formBase");
    }

    public function formFind() {
        $Template= new fnbr\models\Template($this->data->id);
        $filter->idTemplate = $this->data->idTemplate;
        $this->data->query = $Template->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function formNew() {
        $this->data->action = '@Template/save';
        $this->render();
    }

    public function formObject() {
        $this->data->Template = Template::create($this->data->id)->getData();
        $this->render();
    }

    public function formUpdate() {
        $Template= new fnbr\models\Template($this->data->id);
        $this->data->Template = $Template->getData();
        
        $this->data->action = '@Template/save/' .  $this->data->id;
        $this->render();
    }

    public function formDelete() {
        $Template = new fnbr\models\Template($this->data->id);
        $ok = '>Template/delete/' . $Template->getId();
        $cancelar = '>Template/formObject/' . $Template->getId();
        $this->renderPrompt('confirmation', "Confirma remoção do Template [{$model->getDescription()}] ?", $ok, $cancelar);
    }

    public function lookup() {
        $model = new fnbr\models\Template();
        $filter->idTemplate = $this->data->idTemplate;
        $this->data->query = $model->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function save() {
            $Template = new fnbr\models\Template($this->data->Template);
            $Template->save();
            $go = '>Template/formObject/' . $Template->getId();
            $this->renderPrompt('information','OK',$go);
    }

    public function delete() {
            $Template = new fnbr\models\Template($this->data->id);
            $Template->delete();
            $go = '>Template/formFind';
            $this->renderPrompt('information',"Template [{$this->data->idTemplate}] removido.", $go);
    }
*/
}