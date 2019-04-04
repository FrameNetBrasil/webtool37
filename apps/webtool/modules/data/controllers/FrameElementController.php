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



class FrameElementController extends MController {

    public function main() {
        $this->render("formBase");
    }

    public function lookupData(){
        $model = new fnbr\models\FrameElement();
        $criteria = $model->listForLookup($this->data->id);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }

    public function lookupDataExtraThematic(){
        $data = [
            ['name' => ''],
            ['name' => 'Apparent_conclusion'],
            ['name' => 'Beneficiary'],
            ['name' => 'Circumstances'],
            ['name' => 'Co-participant'],
            ['name' => 'Concessive'],
            ['name' => 'Condition'],
            ['name' => 'Containing_event'],
            ['name' => 'Coordinated_event'],
            ['name' => 'Correlated_variable'],
            ['name' => 'Cotheme'],
            ['name' => 'Degree'],
            ['name' => 'Depictive'],
            ['name' => 'Duration'],
            ['name' => 'Event_description'],
            ['name' => 'Excess'],
            ['name' => 'Explanation'],
            ['name' => 'External_cause'],
            ['name' => 'Frequency'],
            ['name' => 'Internal_cause'],
            ['name' => 'Iteration'],
            ['name' => 'Location_of_protagonist'],
            ['name' => 'Maleficiary'],
            ['name' => 'Particular_iteration'],
            ['name' => 'Point_of_contact'],
            ['name' => 'Re_encoding'],
            ['name' => 'Recipient'],
            ['name' => 'Reciprocation'],
            ['name' => 'Role'],
            ['name' => 'Subregion']
        ];
        $this->renderJSON(json_encode($data));
    }

    /*
    public function formFind() {
        $FrameElement= new fnbr\models\FrameElement($this->data->id);
        $filter->idFrameElement = $this->data->idFrameElement;
        $this->data->query = $FrameElement->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function formNew() {
        $this->data->action = '@FrameElement/save';
        $this->render();
    }

    public function formObject() {
        $this->data->FrameElement = FrameElement::create($this->data->id)->getData();
        $this->render();
    }

    public function formUpdate() {
        $FrameElement= new fnbr\models\FrameElement($this->data->id);
        $this->data->FrameElement = $FrameElement->getData();
        
        $this->data->action = '@FrameElement/save/' .  $this->data->id;
        $this->render();
    }

    public function formDelete() {
        $FrameElement = new fnbr\models\FrameElement($this->data->id);
        $ok = '>FrameElement/delete/' . $FrameElement->getId();
        $cancelar = '>FrameElement/formObject/' . $FrameElement->getId();
        $this->renderPrompt('confirmation', "Confirma remoção do FrameElement [{$model->getDescription()}] ?", $ok, $cancelar);
    }

    public function lookup() {
        $model = new fnbr\models\FrameElement();
        $filter->idFrameElement = $this->data->idFrameElement;
        $this->data->query = $model->listByFilter($filter)->asQuery();
        $this->render();
    }

    public function save() {
            $FrameElement = new fnbr\models\FrameElement($this->data->FrameElement);
            $FrameElement->save();
            $go = '>FrameElement/formObject/' . $FrameElement->getId();
            $this->renderPrompt('information','OK',$go);
    }

    public function delete() {
            $FrameElement = new fnbr\models\FrameElement($this->data->id);
            $FrameElement->delete();
            $go = '>FrameElement/formFind';
            $this->renderPrompt('information',"FrameElement [{$this->data->idFrameElement}] removido.", $go);
    }
    */
}