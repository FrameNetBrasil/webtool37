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

class MLogin
{

    private $login;
    public $time;
    private $name;
    private $userData;
    private $idUser;
    private $profile;
    private $isAdmin;
    private $idSession;
    private $rights;
    private $groups;
    private $idPerson;
    private $lastAccess;
    private $weakPass;

    public function __construct($user = '', $name = '', $idUser = '')
    {
        if ($user instanceof PersistentObject) { // it can be a User object
            $this->setUser($user);
        } else { // $user is the login string
            $this->login = $user;
            $this->name = $name;
            $this->idUser = $idUser;
            $this->isAdmin = false;
        }
        $this->time = time();
    }

    public function isUserLogged(PersistentObject $user)
    {
        return $this->login == $user->getLogin();
    }

    public function setUser($user) {
        $this->login = $user->getLogin();
        if (method_exists($user,'getForRegisterLogin')) {
            $user->getForRegisterLogin();
        }
        if (method_exists($user,'getProfile')) {
            $this->profile = $user->getProfile();
        }
        $this->name = $user->getName();
        $this->idUser = $user->getId();
        $this->setGroups($user->getArrayGroups());
        $this->setRights($user->getRights());
        $this->weakPass = $user->weakPassword();
        $this->weakPass = false;
    }

    public function setProfile($user, $profile)
    {
        $this->profile = $profile;
        $this->name = $user->getName();
        $this->idUser = $user->getId();
        $this->setGroups($user->getArrayGroups($profile));
        $this->setRights($user->getRights($profile));
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getUserData($module)
    {
        return $this->userData[$module];
    }

    public function setUserData($module, $data)
    {
        $this->userData[$module] = $data;
    }

    public function setRights($rights)
    {
        $this->rights = $rights;
    }

    public function getRights($transaction = '')
    {
        if ($transaction) {
            return array_key_exists($transaction, $this->rights) ? $this->rights[$transaction] : null;
        }
        return $this->rights;
    }

    public function setGroups($groups)
    {
        $this->groups = $groups;
        $this->isAdmin(array_key_exists('ADMIN', $groups));
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function isAdmin($isAdmin = null)
    {
        if ($isAdmin !== NULL) {
            $this->isAdmin = $isAdmin;
        }
        return $this->isAdmin;
    }

    public function isMemberOf($group)
    {
        return Manager::getPerms()->isMemberOf($group);
    }

    public function isWeakPassword()
    {
        return $this->weakPass;
    }

    public function setIdPerson($idPerson)
    {
        $this->idPerson = $idPerson;
    }

    public function setLastAccess($data)
    {
        $this->lastAccess->tsIn = $data->tsIn;
        $this->lastAccess->tsOut = $data->tsOut;
        $this->lastAccess->remoteAddr = $data->remoteAddr;
    }

    public function isModuleAdmin($module)
    {
        $group = 'ADMIN' . strtoupper($module);
        return array_key_exists($group, $this->groups);
    }

    public function getUser()
    {
        $cacheHit = isset($this->user)
            && is_a($this->user,'PersistentObject')
            && $this->user->getId() == $this->idUser;

        if (!$cacheHit) {
            $this->user = \Manager::getModelMAD('user', $this->idUser);
        }

        return $this->user;
    }

    /**
     * Método usado para informar o que deve ser serializado.
     * A propriedade user é ignorada para evitar problemas com a serialização antes do carregamento da classe.
     * @return array
     */
    public function __sleep()
    {
        $vars = get_object_vars($this);
        unset($vars['user']);

        return array_keys($vars);
    }

}
