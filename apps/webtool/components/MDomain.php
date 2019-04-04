<?php

class MDomain extends MSelection {

    public function onCreate() {
        parent::onCreate();
        $this->setOptions(array('C'=>'Copa','F'=>'Futebol','T'=>'Turismo'));
    }

}
