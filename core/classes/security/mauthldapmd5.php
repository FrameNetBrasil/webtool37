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

/**
 * Classe de autenticação utilizando uma base LDAP.
 *
 * @category    Maestro
 * @package     Core
 * @subpackage  Security
 * @version     1.0
 * @since       1.0
 */
class MauthLdapMD5 extends MAuth
{

    /**
     * Verifica se a senha fornecida por um usuário é válida
     * utilizando autenticação challenge-response.
     *
     * @param int $userId
     * @param string $challenge
     * @param string $response
     * @return boolean
     */
    public function authenticate($userId, $challenge, $response)
    {
        Manager::logMessage("[LOGIN] Authenticating $userId LdapMD5");
        $login = NULL;

        try {
            if ($this->validate($userId, $challenge, $response)) {

                $user = Manager::getModelMAD('user');
                $user->getByLogin($userId);
                $profile = $user->getProfileAtual();
                $user->getByProfile($profile);
                $login = new MLogin($user);

                if (Manager::getOptions("dbsession")) {
                    $session = Manager::getModelMAD('session');
                    $session->lastAccess($login);
                    $session->registerIn($login);
                }
                $this->setLogin($login);
                $this->setLoginLogUserId($user->getId());
                $this->setLoginLog($login->getLogin());
                Manager::logMessage("[LOGIN] Authenticated $userId LdapMD5");
                return true;
            }
        } catch (Exception $e) {
            Manager::logMessage("[LOGIN] $userId NOT Authenticated LdapMD5 - " . $e->getMessage());
        }

        Manager::logMessage("[LOGIN] $userId NOT Authenticated LdapMD5");
        return false;
    }

    /**
     *
     * @param int $userId
     * @param string $challenge
     * @param string $response
     * @return boolean
     */
    public function validate($userId, $challenge, $response)
    {
        $user = Manager::getModelMAD('user');
        $user->getByLogin($userId);
        $login = $user->getLoginAtivo();

        mdump("Ldap validating userid = $userId - login ativo = $login");

        $filter = "uid=$login";
        mdump("Ldap filter = $filter");

        $mldap = new \MLdap();

        $info = $mldap->search($filter, array('userPassword'));

        mdump($info);

        if ($info['count'] == 0) {
            return false;
        }

        $passLdap = trim($info[0]['userpassword'][0]);
        $hash_pass = md5(trim($login) . ':' .
            MLdap::ldapToMd5($passLdap) . ":" .
            $challenge);

        return $hash_pass == $response;
    }

}
