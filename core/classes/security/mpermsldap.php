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

class MPermsLdap extends MPerms
{

    private $auth;
    public $perms;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->manager->getAuth();
        $this->perms = array
        (
            A_ACCESS => "SELECT",
            A_INSERT => "INSERT",
            A_DELETE => "DELETE",
            A_UPDATE => "UPDATE",
            A_EXECUTE => "EXECUTE",
            A_ADMIN => "SYSTEM"
        );
    }

    public function setAuth($auth)
    {
        $this->auth = $auth;
    }

    public function checkAccess($module, $action, $deny = false, $group = false)
    {
        if ($this->auth->isLogged()) {
            $login = $this->auth->getLogin();  // MLogin object
            $isAdmin = $login->isAdmin(); // Is administrator?
            $rights = $login->rights[$module]; // user rights
            if (!$rights) {
                $login->setRights($this->getRights($login->id));
            }
            $ok = @in_array($action, $login->rights[$module]);

            if (!$ok && $group) {
                $groups = $this->getGroupsAllowed($module, $action);
                $ok = sizeof(array_intersect($groups, $login->groups)) > 0;
            }
        }

        if (!$ok && $deny) {

            $msg = _M('Access Denied') . "<br><br>\n" .
                '<center><big><i><font color=red>' . _M('Transaction: ') . "$transaction</font></i></big></center><br><br>\n" .
                _M('Please inform a valid login/password to access this content.') . "<br>";

            $users = $this->getUsersAllowed($module, $action);

            if ($users) {
                $msg .= "<br><br>\n" . _M('Users with access rights') . ":<ul><li>" . implode('<li>', $users) . '</ul>';
            }

            $go = $this->manager->history->back('action');
            $error = Prompt::error($msg, $go, $caption, '');
            $error->addButton(_M('   Login   '), $this->manager->getActionURL($this->manager->getConf('login.module'), 'login', null, array('return_to' => urlencode($this->manager->history->top()))), '');
            $this->manager->prompt($error, $deny);
            //$this->manager->error($msg, $go);
        }
        return $ok;
    }

    public function getTransactionRights($transaction, $login)
    {
        $user = $this->manager->getBusinessMAD('user');
        $user->getByLogin($login);
        return $user->getTransactionRights($transaction);
    }

    public function getRights($login)
    {
        $base = $this->manager->getConf('login.ldap.base');
        $filter = "(&(objectClass=managerUserPermission)(login=$login))";

        $this->manager->auth->connect();

        $sr = ldap_search($this->manager->auth->conn, $base, $filter, array('managermodulename', 'managermoduleaction'));
        $info = ldap_get_entries($this->manager->auth->conn, $sr);

        $rights = array();
        for ($i = 0; $i < $info['count']; $i++) {
            $module = $info[$i]['managermodulename'][0];
            $rights[$module] = array();
            for ($j = 0; $j < $info[$i]['managermoduleaction']['count']; $j++) {
                $rights[$module][] = $info[$i]['managermoduleaction'][$j];
            }
        }
        return $rights;
    }

    public function getGroups($login)
    {
        $user = $this->manager->getBusinessMAD('user');
        $user->getByLogin($login);
        return $user->getArrayGroups();
    }

    public function getUsersAllowed($module, $action = A_ACCESS)
    {
        $base = $this->manager->getConf('login.ldap.base');
        $filter = "(&(objectClass=managerUserPermission)(managerModuleName=$module)(managerModuleAction=$action))";
        $sr = ldap_search($this->manager->auth->conn, $base, $filter, array('login'));
        $info = ldap_get_entries($this->manager->auth->conn, $sr);

        $users = array();
        for ($i = 0; $i < $info['count']; $i++) {
            $users[] = $info[$i]['login'][0];
        }
        return $users;
    }

    public function getGroupsAllowed($module, $action = A_ACCESS)
    {
        $base = $this->manager->getConf('login.ldap.base');
        $filter = "(&(objectClass=managerGroupPermission)(managerModuleName=$module)(managerModuleAction=$action))";
        $sr = ldap_search($this->manager->auth->conn, $base, $filter, array('managergroup'));
        $info = ldap_get_entries($this->manager->auth->conn, $sr);

        $groups = array();
        for ($i = 0; $i < $info['count']; $i++) {
            $groups[] = $info[$i]['managergroup'][0];
        }
        return $groups;
    }

}

