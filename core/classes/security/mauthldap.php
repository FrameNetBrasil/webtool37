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

class MAuthLdap extends MAuth
{

    public $login;  // objeto Login
    public $iduser; // iduser do usuario corrente
    public $module; // authentication module;
    public $conn; //the ldap connection

    public function connect()
    {
        $host = $this->manager->getConf('login.ldap.host');
        $port = $this->manager->getConf('login.ldap.port');
        $user = $this->manager->getConf('login.ldap.user');
        $pass = $this->manager->getConf('login.ldap.password');
        $this->conn = ldap_connect($host, $port);
        ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($this->conn, $user, $pass);

        if (!$r) {
            $prompt = _M('Error on ldap connection!', $module);
            print($prompt);
            exit;
        }
        return true;
    }

    public function __destruct()
    {
        ldap_close($this->conn);
    }

    public function __construct()
    {
        parent::__construct();
        $this->connect();
    }

    public function authenticate($user, $pass, $log = true)
    {
        $base = $this->manager->getConf('login.ldap.base');
        $custom = $this->manager->getConf('login.ldap.custom');
        $schema = $this->manager->getConf('login.ldap.schema');
        $attr = $this->manager->getConf('login.ldap.userName');
        $l = $this->manager->getConf('login.ldap.login');
        $idPerson = $this->manager->getConf('login.ldap.idperson');
        $vars = array(
            '%domain%' => $_SERVER['HOST_NAME'],
            '%login%' => $user,
            '%password%' => md5($pass),
            'AND(' => '&(',
            'OR(' => '|(',
        );
        switch ($schema) {
            case 'manager':
                $search = '(&(login=' . $user . ')(password=' . md5($pass) . '))';
                $login = false;
                break;
            case 'system':
                $search = 'uid=' . $user;
                $login = true;
                break;
            default:
                if ($custom) {
                    $search = strtr($custom, $vars);
                } else {
                    $search = strtr('(&(|(uid=%login%)(login=%login%))(objectClass=managerUser))', $vars);
                }
                $login = null;
        }
        $sr = ldap_search($this->conn, $base, $search, array('dn', $attr, 'password', 'managerGroup', $l, $idPerson));

        $info = ldap_get_entries($this->conn, $sr);

        for ($i = 0; $i < $info['count']; $i++) {
            $bind = $exists = false;
            if ($info[$i]['dn']) {
                if (!$login) {
                    $exists = $info[$i]['password'][0] == md5($pass);
                }
                if (!$exists && (($login) || is_null($login))) {
                    $bind = ldap_bind($this->conn, $info[$i]['dn'], $pass);
                }
                if ($bind || $exists) {
                    $r = true;
                    break;
                }
            }
        }
        if ($l)
            $user = $info[$i][$l][0];

        $groups = array();
        if ($info[$i]['managergroup']['count'] > 0) {
            unset($info[$i]['managergroup']['count']);
            $groups = $info[$i]['managergroup'];
        }

        if ($log && $r) {
            $login = new MLogin($user, $pass, $info[$i][$attr][0], 0);
            $login->setIdPerson($info[$i][$idPerson][0]);
            $login->setGroups($groups);
            $this->setLogin($login);
        }
        return $r;
    }

}
