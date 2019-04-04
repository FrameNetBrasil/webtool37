<?php

class MDialog extends MControl
{

    public $buttons;
    public $help;
    public $tools;
    public $toolbar;

    public function __construct($title = '', $close = '')
    {
        parent::__construct('mdialog');
        $this->property->title = $title;
        $this->property->close = $close;
        $this->buttons = NULL;
        $this->help = NULL;
        $this->toolbar = NULL;
        $this->tools = NULL;
    }

    public function addControl($control)
    {
        $className = ($control->className == 'mhtml') ? $control->tag : $control->className;
        if (($className == 'buttons') || ($className == 'help') || ($className == 'tools') || ($className == 'toolbar')) {
            $this->$className = $control;
        } else {
            if ($control->className == 'mform') {
                $control->style->border = false;
            }
            parent::addControl($control);
        }
    }

    public function setButtons($buttons)
    {
        foreach ($buttons as $button) {
            $this->buttons->addControl($button);
        }
    }

    public function generateButtons()
    {
        $inner = "";
        $control = $this->buttons;
        if ($control->hasItems()) {
            foreach ($control->controls as $button) {
                if ($button->action == '') {
                    $button->action = 'POST';
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

    public function generateTools()
    {
        if ($this->tools) {
            $id = $this->property->id . '_tools';
            $inner = "";
            $control = $this->tools;
            if ($control->hasItems()) {
                foreach ($control->controls as $tool) {
                    $inner .= $tool->generate();
                }
            }
            $this->options->toolbar = '#' . $id;
            return "<div id='{$id}'>{$inner}</div>";
        } else {
            return "";
        }
    }

}
