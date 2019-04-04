<?php

use fnbr\models\Base;

class mfnFilter extends MFilter {

    public function preProcess() {
        $data = Manager::getData();
        if (Manager::isLogged()) {
            $login = Manager::getLogin();
            $userIdLanguage = $login->getUser()->getConfigData('fnbrIdLanguage');
        }
        $idLanguage = $data->lang;
        if ($idLanguage == '') {
            $idLanguage = Manager::getSession()->idLanguage;
            if ($idLanguage == '') {
                $idLanguage = $userIdLanguage;
                if ($idLanguage == '') {
                    $idLanguage = 2;
                }
            }
        }
        Manager::getSession()->idLanguage = $idLanguage;
        $db = $data->datasource ? : Manager::getConf('fnbr.db');
        Manager::setConf('fnbr.db', $db);
        Manager::setConf('options.language', Base::languages()[$idLanguage]);
    }

}

