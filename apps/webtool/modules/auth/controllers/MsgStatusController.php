<?php

Manager::import("fnbr\auth\models\*");

class MsgStatusController extends MController {

    public function main() {
        $this->render("formBase");
    }

    public function formFind() {
        $auth_msgstatus= new fnbr\models\Auth_msgstatus($this->data->id);
        $filter->idMsgStatus = $this->data->idMsgStatus;
        $this->data->query = $auth_msgstatus->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function formNew() {
        $this->data->action = '@auth/auth_msgstatus/save';
        $this->render();
    }

    public function formObject() {
        $this->data->auth_msgstatus = Auth_msgstatus::create($this->data->id)->getData();
        $this->render();
    }

    public function formUpdate() {
        $auth_msgstatus= new fnbr\models\Auth_msgstatus($this->data->id);
        $this->data->auth_msgstatus = $auth_msgstatus->getData();
        
        $this->data->action = '@auth/auth_msgstatus/save/' .  $this->data->id;
        $this->render();
    }

    public function formDelete() {
        $auth_msgstatus = new fnbr\models\Auth_msgstatus($this->data->id);
        $ok = '>auth/auth_msgstatus/delete/' . $auth_msgstatus->getId();
        $cancelar = '>auth/auth_msgstatus/formObject/' . $auth_msgstatus->getId();
        $this->renderPrompt('confirmation', "Confirma remoção do Auth_msgstatus [{$model->getDescription()}] ?", $ok, $cancelar);
    }

    public function lookup() {
        $model = new fnbr\models\Auth_msgstatus();
        $filter->idMsgStatus = $this->data->idMsgStatus;
        $this->data->query = $model->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function save() {
            $auth_msgstatus = new fnbr\models\Auth_msgstatus($this->data->auth_msgstatus);
            $auth_msgstatus->save();
            $go = '>auth/auth_msgstatus/formObject/' . $auth_msgstatus->getId();
            $this->renderPrompt('information','OK',$go);
    }

    public function delete() {
            $auth_msgstatus = new fnbr\models\Auth_msgstatus($this->data->id);
            $auth_msgstatus->delete();
            $go = '>auth/auth_msgstatus/formFind';
            $this->renderPrompt('information',"Auth_msgstatus [{$this->data->idMsgStatus}] removido.", $go);
    }

}