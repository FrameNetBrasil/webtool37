<?php

Manager::import("fnbr\auth\models\*");

class TransactionController extends MController {

    public function main() {
        $this->render("formBase");
    }

    public function formFind() {
        $Transaction= new fnbr\models\Transaction($this->data->id);
        $filter->idTransaction = $this->data->idTransaction;
        $this->data->query = $Transaction->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function formNew() {
        $this->data->action = '@auth/Transaction/save';
        $this->render();
    }

    public function formObject() {
        $this->data->Transaction = Transaction::create($this->data->id)->getData();
        $this->render();
    }

    public function formUpdate() {
        $Transaction= new fnbr\models\Transaction($this->data->id);
        $this->data->Transaction = $Transaction->getData();
        
        $this->data->action = '@auth/Transaction/save/' .  $this->data->id;
        $this->render();
    }

    public function formDelete() {
        $Transaction = new fnbr\models\Transaction($this->data->id);
        $ok = '>auth/Transaction/delete/' . $Transaction->getId();
        $cancelar = '>auth/Transaction/formObject/' . $Transaction->getId();
        $this->renderPrompt('confirmation', "Confirma remoção do Transaction [{$model->getDescription()}] ?", $ok, $cancelar);
    }

    public function lookup() {
        $model = new fnbr\models\Transaction();
        $filter->idTransaction = $this->data->idTransaction;
        $this->data->query = $model->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function save() {
            $Transaction = new fnbr\models\Transaction($this->data->Transaction);
            $Transaction->save();
            $go = '>auth/Transaction/formObject/' . $Transaction->getId();
            $this->renderPrompt('information','OK',$go);
    }

    public function delete() {
            $Transaction = new fnbr\models\Transaction($this->data->id);
            $Transaction->delete();
            $go = '>auth/Transaction/formFind';
            $this->renderPrompt('information',"Transaction [{$this->data->idTransaction}] removido.", $go);
    }

}