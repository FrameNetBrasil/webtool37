<?php

Manager::import("fnbr\auth\models\*");

class GroupController extends MController {

    public function main() {
        $this->render("formBase");
    }

    public function formFind() {
        $Group= new fnbr\auth\models\Group($this->data->id);
        $filter->idGroup = $this->data->idGroup;
        $this->data->query = $Group->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function formNew() {
        $this->data->action = '@auth/Group/save';
        $this->render();
    }

    public function formObject() {
        $this->data->Group = Group::create($this->data->id)->getData();
        $this->render();
    }

    public function formUpdate() {
        $Group= new fnbr\auth\models\Group($this->data->id);
        $this->data->Group = $Group->getData();
        
        $this->data->action = '@auth/Group/save/' .  $this->data->id;
        $this->render();
    }

    public function formDelete() {
        $Group = new fnbr\auth\models\Group($this->data->id);
        $ok = '>auth/Group/delete/' . $Group->getId();
        $cancelar = '>auth/Group/formObject/' . $Group->getId();
        $this->renderPrompt('confirmation', "Confirma remoção do Group [{$model->getDescription()}] ?", $ok, $cancelar);
    }

    public function lookup() {
        $model = new fnbr\auth\models\Group();
        $filter->idGroup = $this->data->idGroup;
        $this->data->query = $model->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function save() {
            $Group = new fnbr\auth\models\Group($this->data->Group);
            $Group->save();
            $go = '>auth/Group/formObject/' . $Group->getId();
            $this->renderPrompt('information','OK',$go);
    }

    public function delete() {
            $Group = new fnbr\auth\models\Group($this->data->id);
            $Group->delete();
            $go = '>auth/Group/formFind';
            $this->renderPrompt('information',"Group [{$this->data->idGroup}] removido.", $go);
    }

}