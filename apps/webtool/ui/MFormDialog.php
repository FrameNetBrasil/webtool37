<?php

class MFormDialog extends MForm
{

    public function generateFields()
    {
        if (count($control->fields->controls) == 1) {
            $style="style=border-spacing:0px;";
        }    
        $fields = "<div class='mFormContainer' {$style}>";
        $control = $this->fields;
        if ($control->hasItems()) {
            foreach ($control->controls as $field) {
                if ($field->className == 'mhiddenfield') {
                    $fields .= $field->generate();
                } else {
                    $mfieldlabel = new mfieldlabel(['id' => $field->property->id, 'text' => $field->property->label]);
                    if ($this->property->layout == 'horizontal') {
                        //$mfieldlabel->setClass($this->labelClass);
                    }
                    $label = $mfieldlabel->generate();
                    if ($label) {
                        $formGroup = "<div class=\"mFormColumn\">{$label}</div>"."<div class=\"mFormColumn\">{$field->generate()}</div>";
                    } else {
                        $formGroup = "<div class=\"mFormColumn\">{$field->generate()}</div>";
                    }
                    if (($field->className == 'mvcontainer') || ($field->className == 'mhcontainer')) {
                	$fields .= "</div>";
                        $fields .= $field->generate();
                        $fields .= "<div class='mFormContainer'>";
                    } else {
                        // usa a classe form-group do bootstrap
                        $fields .= "<div class=\"mFormRow\">{$formGroup}</div>";
                    }    
                }
            }
        }
	$fields .= "</div>";
        return $fields;
    }

    public function generateTools()
    {
        $inner = "";
        $control = $this->tools;
        if ($control->hasItems()) {
            foreach ($control->controls as $tool) {
                $inner .= $tool->generate();
            }
        }
        return $inner;
    }
    
    public function generateForm()
    {
        $this->property->action = $this->property->action ? : Manager::getCurrentURL();
        MUtil::setIfNull($this->property->method, 'POST');
        MUtil::setIfNull($this->style->width, "100%");
        $this->property->role = "form";
        $fields = $buttons = $help = $tools = "";
        if ($this->fields != NULL) {
            $fields = $this->generateFields();
        }
        if ($this->buttons != NULL) {
            $buttons = $this->generateButtons();
        }
        if ($this->help != NULL) {
            $help = $this->generateHelp();
        }
        if ($this->tools != NULL) {
            $tools = $this->generateTools();
        }
        // menubar
        if ($this->property->menubar) {
            $menubar = $this->property->menubar->generate();
        }
        
        // por default, o método de submissão é POST
        MUtil::setIfNull($this->property->method, "POST");
        
        if ($this->property->onsubmit) {
            $this->page->onSubmit($this->property->onsubmit, $this->property->id);
        }
        
        // se o form tem fields com validators, define onSubmit
        $validators = '';
        if (count($this->property->toValidate)) {
            $this->page->onSubmit("$('#{$this->property->id}').form('validate')", $this->property->id);
            $validators = implode(',', $this->property->bsValidator);
        }
        
        // obtem o codigo html via template
        $inner = $this->painter->fetch('mformdialog', $this, [
            'fields' => $fields,
            'buttons' => $buttons,
            'help' => $help,
            'tools' => $tools,
            'validators' => $validators,
            'menubar' => $menubar
        ]);
        return $inner;
    }
    
    
    public function generate()
    {
        // dialog
        $dialog = new MDialog();
        $dialog->setId($this->property->id . '_dialog');
        $dialog->property->title = $this->property->title;
        $dialog->property->close = $this->property->close;
        $dialog->property->onClose = $this->property->onClose;
        $dialog->property->class = $this->property->class;
        $dialog->style->width = $this->style->width;
        $dialog->options = $this->options;
        if ($this->buttons) {
            $dialog->options->buttons = '#' . $this->property->id . '_buttons';
        }
        if ($this->tools) {
            $dialog->options->toolbar = '#' . $this->property->id . '_tools';
        }
        $dialog->property->state = "open";        
        $dialog->options->border = isset($this->style->border) ? $this->style->border : true;
        $this->options = new \stdClass(); // remove as options do form
        $dialog->inner = $this->generateForm();
        return $dialog->generate();
    }

}
