<?php

Manager::import("fnbr\auth\models\*");

class MessageBoxController extends MController {

    public function main() {
        $this->render("formBase");
    }

    public function formFind() {
        $auth_messagebox= new fnbr\models\Auth_messagebox($this->data->id);
        $filter->idMessageBox = $this->data->idMessageBox;
        $this->data->query = $auth_messagebox->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function formNew() {
        $this->data->action = '@auth/auth_messagebox/save';
        $this->render();
    }

    public function formObject() {
        $this->data->auth_messagebox = Auth_messagebox::create($this->data->id)->getData();
        $this->render();
    }

    public function formUpdate() {
        $auth_messagebox= new fnbr\models\Auth_messagebox($this->data->id);
        $this->data->auth_messagebox = $auth_messagebox->getData();
        $this->data->auth_messagebox->idUserDesc = $auth_messagebox->getUser()->getDescription();
	$this->data->auth_messagebox->idMsgStatusDesc = $auth_messagebox->getMsgStatus()->getDescription();
	$this->data->auth_messagebox->idMessageDesc = $auth_messagebox->getMessage()->getDescription();
	
        $this->data->action = '@auth/auth_messagebox/save/' .  $this->data->id;
        $this->render();
    }

    public function formDelete() {
        $auth_messagebox = new fnbr\models\Auth_messagebox($this->data->id);
        $ok = '>auth/auth_messagebox/delete/' . $auth_messagebox->getId();
        $cancelar = '>auth/auth_messagebox/formObject/' . $auth_messagebox->getId();
        $this->renderPrompt('confirmation', "Confirma remoção do Auth_messagebox [{$model->getDescription()}] ?", $ok, $cancelar);
    }

    public function lookup() {
        $model = new fnbr\models\Auth_messagebox();
        $filter->idMessageBox = $this->data->idMessageBox;
        $this->data->query = $model->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function save() {
            $auth_messagebox = new fnbr\models\Auth_messagebox($this->data->auth_messagebox);
            $auth_messagebox->save();
            $go = '>auth/auth_messagebox/formObject/' . $auth_messagebox->getId();
            $this->renderPrompt('information','OK',$go);
    }

    public function delete() {
            $auth_messagebox = new fnbr\models\Auth_messagebox($this->data->id);
            $auth_messagebox->delete();
            $go = '>auth/auth_messagebox/formFind';
            $this->renderPrompt('information',"Auth_messagebox [{$this->data->idMessageBox}] removido.", $go);
    }

}