<?php

class MForm extends MControl
{

    public $toValidate = array();
    public $bsValidator = array();
    public $fields;
    public $buttons;
    public $help;
    public $tools;
    public $toolbar;

    public function __construct($title = '', $close = '')
    {
        parent::__construct('mform');
        $this->property->title = $title;
        $this->property->close = $close;
        $this->fields = NULL;
        $this->buttons = NULL;
        $this->help = NULL;
        $this->tools = NULL;
    }

    public function addControl($control)
    {
        parent::addControl($control);
        $control->setForm($this);
        $className = ($control->className == 'mhtml') ? $control->tag : $control->className;
        if (($className == 'fields') || ($className == 'buttons') || ($className == 'help') || ($className == 'tools') || ($className == 'toolbar')) {
            $this->$className = $control;
        } else {
            if ($this->fields == NULL) {
                $this->fields = new MControl();
            }
            $this->fields->addControl($control);
        }
    }

    public function onAfterCreate()
    {
        if ($this->property->load) {
            $this->setData($this->property->load);
        }
    }

    public function onLoad()
    {
        parent::onLoad();
        $this->createFields();
    }

    public function createFields()
    {
        
    }

    public function setFields($fields)
    {
        foreach ($fields as $field) {
            $this->fields->addControl($field);
        }
    }

    public function setButtons($buttons)
    {
        foreach ($buttons as $button) {
            $this->buttons->addControl($button);
        }
    }

    public function generateFields()
    {
        $fields = '';
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
                        $formGroup = "<div class=\"mFormColumn\">{$label}</div>" . "<div class=\"mFormColumn\">{$field->generate()}</div>";
                    } else {
                        //$formGroup = "<div class=\"mFormColumn\">{$field->generate()}</div>";
                        $formGroup = "<div class=\"mFormColumn\"></div>" . "<div class=\"mFormColumn\">{$field->generate()}</div>";
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
        return $fields;
    }

    public function generateButtons()
    {
        $inner = "";
        $control = $this->buttons;
        if ($control->hasItems()) {
            foreach ($control->controls as $button) {
                if ($button->action == '') {
                    $button->action = 'POST';
                } else if (strpos($button->action, '|') === false) {
                    $button->action .= '|' . $this->getId();
                }    
                $inner .= $button->generate();
            }
        }
        return $inner;
    }

    public function generateHelp()
    {
        $inner = "";
        $control = $this->help;
        if ($control->hasItems()) {
            foreach ($control->controls as $help) {
                $inner .= $help->generate();
            }
        }
        return $inner;
    }

    public function generate()
    {
        // panel
        $panel = new MPanel();
        $panel->property->title = $this->property->title;
        $panel->style->width = $this->style->width;
        $panel->property->close = $this->property->close;
        $panel->property->class = $this->property->class;
        //mdump('--');
        //mdump($this->style->border);
        $panel->options->border = isset($this->style->border) ? $this->style->border : false;
        $panel->generate(); //gera o panel para obter todos os atributos
        // propriedades
        $this->property->action = $this->property->action ? : Manager::getCurrentURL();
        MUtil::setIfNull($this->property->method, 'POST');
        MUtil::setIfNull($this->style->width, "100%");
        $this->property->role = "form";
        // define o layout com base na classe bootstrap do form
        MUtil::setIfNull($this->property->layout, "horizontal");
        $this->setClass("form-{$this->property->layout}");
        // neste tema o mform é constituído de 3 blocos principais: fields, buttons e help
        $fields = $buttons = $help = "";
        if ($this->fields != NULL) {
            $fields = $this->generateFields();
        }
        if ($this->buttons != NULL) {
            $buttons = $this->generateButtons();
        }
        if ($this->help != NULL) {
            $help = $this->generateHelp();
        }
        // toolbar
        if ($this->toolbar) {
            $this->toolbar->tag = 'header';
            $this->toolbar->setClass('datagrid-toolbar');
            $toolbar = $this->toolbar->generate();
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
            $this->page->onSubmit("$('#{$this->property->id}').form('validate')", $this->id);
            $validators = implode(',', $this->property->bsValidator);
        }
        
        // obtem o codigo html via template
        $result = $this->painter->fetch('mform', $this, [
            'panel' => $panel,
            'fields' => $fields,
            'buttons' => $buttons,
            'help' => $help,
            'validators' => $validators,
            'menubar' => $menubar,
            'toolbar' => $toolbar
        ]);
        return $result;
    }

}
