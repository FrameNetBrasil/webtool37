<?php
/**
 * MValidator
 * Classe auxiliar para tratamento de validators em controles de formulários.
 * Adiciona options de validação para o controle e define a propriedade 
 * 'bsValidator' (validação Bootstrap) do MForm, se necessário.
 */
class MValidator {
    
    private $control;
    private $validType = array();
    private $hidden = '';
    
    public static function process($control) {
        $mvalidator = new MValidator($control);
        return $mvalidator->processValidators();
    }

    public function __construct($control) {
        $this->control = $control;
    }

    public function processValidators() {
        foreach($this->control->validators as $validator) {
            if ($validator->disabled) {
                continue;
            }
            $this->control->addClass('easyui-validatebox');
            $method = "add" . $validator->type;
            $this->$method($validator);
            $this->control->property->options['invalidMessage'] = $validator->message;
            if (count($this->validType)) {
                if (count($this->validType) > 1) {
                    $validType = '{' . implode(',', $this->validType) . '}';
                    $this->control->property->options['validType'] = (object)$validType;
                } else {
                    $this->control->property->options['validType'] = $this->validType[0];
                }
            }
            $this->control->form->toValidate[] = $this->control;
        }
    }
    
    private function addRequired($validator) {
        $this->control->property->options['required'] = true;
        $this->control->property->options['missingMessage'] = $validator->message;
    }

    private function addRange($validator) {
        $this->validType[] = "range:['{$this->control->form->id}','{$this->control->id}']";
        $min = $validator->parameter[0];
        $max = $validator->parameter[1];
        $this->control->form->bsValidator[] = "{$this->control->id}: {validators: {between: {min: {$min}, max: {$max}, message:''}}}";
    }
    
    private function addLength($validator) {
        $min = $validator->parameter[0];
        $max = $validator->parameter[1];
        $this->validType[] = "length:[{$min},{$max}]";
    }
    
    private function addEmail($validator) {
        $this->validType[] = "email";
    }

    private function addURL($validator) {
        $this->validType[] = "url";
    }
    
    private function addRegExp($validator) {
        $this->validType[] = "regexp:['{$this->control->form->id}','{$this->control->id}']";
        $this->control->form->bsValidator[] = "{$this->control->id}: {validators: {regexp: {regexp: /{$validator->parameter}/, message:''}}}";
    }

    private function addDateRange($validator) {
        $name = $this->control->id . '_validate';
        $this->hidden .= "<input type='hidden' name='{$name}' value=''/>";
        $this->validType[] = "daterange:['{$this->control->form->id}','{$name}']";
        $min = $validator->parameter[0];
        $max = $validator->parameter[1];
        $this->control->form->bsValidator[] = "{$this->control->id}: {validators: {date: {format: 'DD/MM/YYYY', min: '{$min}', max: '{$max}', message:''}}}";
    }
    
    private function addDate($validator) {
        $name = $this->control->id . '_validate';
        $this->hidden .= "<input type='hidden' name='{$name}' value=''/>";
        $this->validType[] = "date:['{$this->control->form->id}','{$name}']";
        $this->control->form->bsValidator[] = "{$this->control->id}: {validators: {date: {format: 'DD/MM/YYYY', message:''}}}";
    }
            
}
