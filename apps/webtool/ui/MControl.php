<?php

class MControl extends MBaseControl
{
    public $plugin;
    
    public function addTool($object)
    {
        if ($this->toolbar == NULL) {
            $this->toolbar = new \MToolBar();
        }
        $tool = new \MToolButton();
        $tool->attributes->title = $object->title;
        $tool->action = $object->action;
        $tool->iconCls = $object->iconCls ? : $object->icon;
        $tool->plain = isset($object->plain) ? $object->plain : true;
        $tool->size = isset($object->size) ? $object->size : '24px';
        if ($object->text != "") {
            $tool->text = $object->text;
        }
        $this->toolbar->addControl($tool);
    }

    public function addAction($object)
    {
        if ($this->menubar == NULL) {
            $this->menubar = new \MMenuBar();
        }
        $action = new \MMenuBarItem();
        $action->attributes->title = $object->label;
        $action->action = $object->action;
        $action->icon = $object->iconCls ? : $object->icon;
        $action->plain = isset($object->plain) ? $object->plain : true;
        $action->label = $object->label;
        $this->menubar->addControl($action);
    }

}
