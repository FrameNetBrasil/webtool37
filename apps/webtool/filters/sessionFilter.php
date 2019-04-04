<?php

class SessionFilter extends MFilter
{

    public function preProcess()
    {
        $context = $this->frontController->getContext();
        // alteração da configuração dependendo do modulo sendo executado
        $module = $context->getModule();
        if (($module == 'report') || ($module == 'grapher')) {
            Manager::setConf('session.check', false);
            Manager::setConf('login.check', false);
        }
        // alteração da configuração dependendo do controller sendo executado
        $controller = $context->getController();
        if (($controller == 'grapher') || ($controller == 'reports') || ($controller == 'report') || ($controller == 'actions')) {
            Manager::setConf('session.check', false);
            Manager::setConf('login.check', false);
        }
        // é necessário validar a sessão?
        if (Manager::getConf('login.check') || Manager::getConf('session.check')) {
            $timeout = Manager::getSession()->checkTimeout(Manager::getConf('session.exception'));
        }
        if ($timeout) {
            $this->frontController->canCallHandler(false);
            $url = Manager::getURL(Manager::getApp() . '/main');
            $this->frontController->setResult(new MRedirect(NULL, $url));
        }
    }

}
