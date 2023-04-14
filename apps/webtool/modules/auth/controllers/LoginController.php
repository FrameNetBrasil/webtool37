<?php

use fnbr\auth\models\User;

class LoginController extends \MController
{

    public function init()
    {
        Manager::checkLogin(false);
    }

    public function logout()
    {
        Manager::getAuth()->logout();
        $main = Manager::getURL('main');
        $this->redirect($main);
    }

    public function authenticate()
    {
        if ($this->data->datasource == '') {
            $this->data->datasource = Manager::getConf('fnbr.db'); //$this->renderPrompt('error', 'Inform database name.');
        }
        Manager::setConf('fnbr.db', $this->data->datasource);
        $user = new User();
        $user->getByLogin($this->data->user);
        $groups = $user->getArrayGroups();
        //if ($groups['ADMIN']) {
            $auth = Manager::getAuth();
            $this->data->result = $auth->authenticate($this->data->user, $this->data->challenge, $this->data->response);
            if ($this->data->result) {
                $user = Manager::getLogin()->getUser();
                $this->data->idLanguage = $user->getConfigData('fnbrIdLanguage');
                if ($this->data->idLanguage == '') {
                    $this->data->idLanguage = 1;
                    $user->setConfigData('fnbrIdLanguage', $this->data->idLanguage);
                }
                if ($this->data->ifLanguage == '') {
                    $this->data->ifLanguage = 'en';
                }

                Manager::getSession()->idLanguage = $this->data->idLanguage;
                Manager::getSession()->lang = $this->data->ifLanguage;
                Manager::getSession()->fnbrLevel = $user->getUserLevel();
                $this->redirect(Manager::getURL('main'));
            } else {
                $this->renderPrompt('error', 'Login or password not valid.');
            }
        //} else {
        //    $this->renderPrompt('error', 'Login or password not valid.');
        //}
    }

}
