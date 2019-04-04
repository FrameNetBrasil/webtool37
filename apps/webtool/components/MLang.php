<?php

class MLang extends MSelection {

    public function onCreate() {
        parent::onCreate();
        $this->setOptions(array('pt'=>'Português','en'=>'English','es'=>'Español'));
    }

}

