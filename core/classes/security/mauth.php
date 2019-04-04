<?php

/* Copyright [2011, 2013, 2017] da Universidade Federal de Juiz de Fora
 * Este arquivo é parte do programa Framework Maestro.
 * O Framework Maestro é um software livre; você pode redistribuí-lo e/ou
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada
 * pela Fundação do Software Livre (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil,
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL
 * em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título
 * "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software
 * Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a
 * Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

class MAuth
{

    private $login;  // objeto Login
    public $idUser; // iduser do usuario corrente
    public $module; // authentication module;

    public function __construct()
    {
        $this->module = Manager::$conf['login']['module'];
    }

    public function setLogin($login = false)
    {
        $this->login = $login;
        $this->updateSessionLogin();
        $this->idUser = ($this->login instanceof MLogin ? $this->login->getIdUser() : NULL);
    }

    public function setLoginLogUserId($userId)
    {
        $_SESSION['loginLogUserId'] = $userId;
        // mdump("setLoginLogUserId " . $userId);
    }

    public function getLoginLogUserId()
    {
        // mdump("getLoginLogUserId " . $_SESSION['loginLogUserId']);
        return $_SESSION['loginLogUserId'];
    }

    public function setLoginLog($login)
    {
        $_SESSION['loginLog'] = $login;
    }

    public function getLoginLog()
    {
        return $_SESSION['loginLog'];
    }


    public function getLogin()
    {
        if ($this->login instanceof MLogin) {
            return $this->login;
        } else {
            $login = Manager::getSession()->getValue('__sessionLogin');
            if ($login instanceof MLogin) {
                return $login;
            }
        }
        return null;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function checkLogin()
    {
        Manager::logMessage('[LOGIN] Running CheckLogin');

// if not checking logins, we are done
        if ((!MUtil::getBooleanValue(Manager::$conf['login']['check']))) {
            Manager::logMessage('[LOGIN] I am not checking login today...');
            return true;
        }

// we have a session login?
        $session = Manager::getSession();
        $login = $session->getValue('__sessionLogin');
        $loginMiolo = $_SESSION['login']; // Miolo compatibility
        if ($loginMiolo) {
            if (!($login instanceof MLogin)) { // se ainda não tem login no Maestro...
                $user = Manager::getModelMAD('user');
                $user->getByLogin($loginMiolo->id);
                if (method_exists($user, 'getProfileAtual')) {
                    $profile = $user->getProfileAtual();
                    $user->getByProfile($profile);
                }
                $login = new MLogin($user);
                $this->setLogin($login);
                Manager::logMessage("[LOGIN] Authenticated {$loginMiolo->idkey} from Miolo");
            }
        }
        if ($login instanceof MLogin) {
            if ($login->getLogin()) {
                Manager::logMessage('[LOGIN] Using session login: ' . $login->getLogin());
                $this->setLogin($login);
                return true;
            }
        }

// if we have already a login, assume it is valid and return
        if ($this->login instanceof MLogin) {
            Manager::logMessage('[LOGIN] Using existing login:' . $this->login->getLogin());
            return true;
        }

        Manager::logMessage('[LOGIN] No Login but Login required!');
        return false;
    }

    public function authenticate($userId, $challenge, $response)
    {
        return false;
    }

    public function isLogged()
    {
        if ($this->login instanceof MLogin) {
            return ($this->login->getLogin() != NULL);
        } else {
            $login = Manager::getSession()->getValue('__sessionLogin');
            if ($login instanceof MLogin) {
                return ($login->getLogin() != NULL);
            }
        }
        return false;
    }

    public function logout($forced = '')
    {
        if (Manager::getOptions('dbsession')) {
//$session = $this->manager->getBusinessMAD('session');
//$session->registerOut($this->getLogin());
        }
        $this->setLogin(NULL);
        Manager::getSession()->destroy();
    }

    private function updateSessionLogin()
    {
        Manager::getSession()->setValue('__sessionLogin', $this->login);
        if ($this->login) {
            $this->login->id = !$this->login ?: $this->login->getLogin(); // Miolo compatibility
        }
        $_SESSION['login'] = $this->login; // Miolo compatibility
    }

    public function updateSessionLoginIfIsUserLogged(PersistentObject $user)
    {
        if ($this->login->isUserLogged($user)) {
            $this->login->setUser($user);
            $this->updateSessionLogin();
        }
    }

}
