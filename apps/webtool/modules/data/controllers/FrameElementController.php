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

    public function lookupDataDecorated(){
        $model = new fnbr\models\FrameElement();
        $criteria = $model->listForLookupDecorated($this->data->id);
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

}