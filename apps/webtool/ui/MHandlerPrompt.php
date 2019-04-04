<?php

class MHandlerPrompt {

    static public function handler (MPromptData $prompt) {
        $type = $prompt->type;
        $oPrompt = new MPrompt(["type" => $prompt, "msg" => $prompt->message, "action1" => $prompt->action1, "action2" => $prompt->action2, "event1" => $prompt->event1, "event2" => $prompt->event2]);
        $prompt->setControl($oPrompt);
//        if (!Manager::isAjaxCall()) {
//            Manager::getPage()->onLoad("manager.doPrompt('{$oPrompt->getId()}')");
//        }
    }


}