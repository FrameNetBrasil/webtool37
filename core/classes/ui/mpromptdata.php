<?php

class MPromptData
{

    public $id;
    public $type;
    public $message;
    public $action1;
    public $action2;
    public $content;
    public $object;

    public function __construct($type = '', $message = '', $action1 = '', $action2 = '')
    {
        $this->id = '';
        $this->type = $type;
        $this->message = $message;
        $this->action1 = $action1;
        $this->action2 = $action2;
        $this->content = '';
        $this->object = null;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }

}
