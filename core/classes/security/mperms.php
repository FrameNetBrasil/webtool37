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

class MPerms {

    private $auth;
    private $access;
    public $perms;

    public function __construct() {
        $this->auth = Manager::getAuth();
        $this->perms = array(
            A_ACCESS => "SELECT",
            A_INSERT => "INSERT",
            A_DELETE => "DELETE",
            A_UPDATE => "UPDATE",
            A_EXECUTE => "EXECUTE",
            A_ADMIN => "SYSTEM"
        );
        $this->access = array(
            'A_ACCESS' => A_ACCESS,
            'A_QUERY' => A_ACCESS,
            'A_INSERT' => A_INSERT,
            'A_DELETE' => A_DELETE,
            'A_UPDATE' => A_UPDATE,
            'A_EXECUTE' => A_EXECUTE,
            'A_SYSTEM' => A_ADMIN,
            'A_ADMIN' => A_ADMIN,
        );
    }

    public function getPerms() {
        return $this->perms;
    }

    public function getRight($right) {
        if (!is_numeric($right)) {
            $right = $this->access[$right];
        }
        return $right;
    }

    public function checkAccess($transaction, $access, $deny = false) {
        $module = Manager::getModule();
        $ok = false;
        if (!is_numeric($access)) {
            $access = $this->access[$access];
        }
        if ($this->auth->isLogged()) {
            $login = $this->auth->getLogin();  // MLogin object
            $transaction = strtoupper($transaction); // Transaction name
            $isAdmin = $login->isAdmin(); // Is administrator?
            $rights = (int) $login->getRights($transaction); // user rights
            $rightsInAll = (int) $login->getRights('ALL'); // user rights in all transactions
            $ok = (($rights & $access) == $access) || (($rightsInAll & $access) == $access) || ($isAdmin);

            if ((!$ok) && $deny) {
                $msg = _M('Acesso Negado') . "<br><br>\n" . "<center><big><i><font color=red>" . _M('Transação: ') .
                        "{$transaction}</font></i></big></center><br><br>\n" .
                        _M('Informe um login válido para acessar esta página.') . "<br>";
                throw new ESecurityException($msg);
            }
        } else {
            if ($deny) {
                $currentUrl = urlencode(Manager::getCurrentURL());
                $module = Manager::getConf('login.module');
                $url = Manager::getURL("{$module}/main.login", array('return_to' => $currentUrl));
                Manager::getPage()->redirect($url);
            }
        }
        return $ok;
    }

    public function getTransactionRights($transaction, $login) {
        $user = Manager::getModelMAD('user');
        $user->getByLogin($login);
        return $user->getTransactionRights($transaction);
    }

    public function getRights($login) {
        $user = Manager::getModelMAD('user');
        $user->getByLogin($login);
        return $user->getRights($transaction);
    }

    public function getGroups($login) {
        $user = Manager::getModelMAD('user');
        $user->getByLogin($login);
        return $user->getArrayGroups();
    }

    public function isMemberOf($group) {
        $groups = Manager::getLogin()->getGroups();
        $ok = $groups[strtoupper($group)] || $groups['ADMIN'];
        return $ok;
    }

    public function isAdmin() {
        return $this->auth->getLogin()->isAdmin();
    }

    public function getUsersAllowed($trans, $action = A_ACCESS) {
        $transaction = Manager::getModelMAD('transaction');
        $transaction->getByName($trans);
        return $transaction->getUsersAllowed($action);
    }

    public function getGroupsAllowed($trans, $action = A_ACCESS) {
        $transaction = Manager::getModelMAD('transaction');
        $transaction->getByName($trans);
        return $transaction->getGroupsAllowed($action);
    }

}
