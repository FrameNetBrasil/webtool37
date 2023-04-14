<?php

Manager::import("fnbr\auth\models\*");

class LogController extends MController {

    public function main() {
        $this->render("formBase");
    }

    public function formFind() {
        $Log= new fnbr\models\Log($this->data->id);
        $filter->idLog = $this->data->idLog;
        $this->data->query = $Log->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function formNew() {
        $this->data->action = '@auth/Log/save';
        $this->render();
    }

    public function formObject() {
        $this->data->Log = Log::create($this->data->id)->getData();
        $this->render();
    }

    public function formUpdate() {
        $Log= new fnbr\models\Log($this->data->id);
        $this->data->Log = $Log->getData();
        
        $this->data->action = '@auth/Log/save/' .  $this->data->id;
        $this->render();
    }

    public function formDelete() {
        $Log = new fnbr\models\Log($this->data->id);
        $ok = '>auth/Log/delete/' . $Log->getId();
        $cancelar = '>auth/Log/formObject/' . $Log->getId();
        $this->renderPrompt('confirmation', "Confirma remoção do Log [{$model->getDescription()}] ?", $ok, $cancelar);
    }

    public function lookup() {
        $model = new fnbr\models\Log();
        $filter->idLog = $this->data->idLog;
        $this->data->query = $model->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function save() {
            $Log = new fnbr\models\Log($this->data->Log);
            $Log->save();
            $go = '>auth/Log/formObject/' . $Log->getId();
            $this->renderPrompt('information','OK',$go);
    }

    public function delete() {
            $Log = new fnbr\models\Log($this->data->id);
            $Log->delete();
            $go = '>auth/Log/formFind';
            $this->renderPrompt('information',"Log [{$this->data->idLog}] removido.", $go);
    }

}