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
 * Classe de serviços ldap.
 *
 * @category    Maestro
 * @package     Core
 * @subpackage  Services
 * @version     1.0
 * @since       1.0
 */
class MLdap
{

    /**
     * Servidor Ldap
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * Nome do usuario para conexao na base Ldap
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * Base na arvore ldap a partir de onde serão feitas as operações
     * @var string
     */
    private $base;

    /**
     * Instância de uma conexão Ldap
     * @var resource
     */
    private $conn;

    public function __construct()
    {
        if ($this->isEnabled()) {
            $this->connect();
        }
    }

    public function isEnabled()
    {
        return (
            \Manager::getConf('login.class') == 'MAuthLdapMD5' &&
            \Manager::getConf('login.ldap.host') != false
        );
    }

    /**
     * Tenta se conectar à base Ldap de acordo com os dados em conf.php
     * @return boolean
     */
    private function connect()
    {
        $this->host = \Manager::getConf('login.ldap.host');
        $this->port = \Manager::getConf('login.ldap.port');
        $this->user = \Manager::getConf('login.ldap.user');
        $this->pass = \Manager::getConf('login.ldap.password');
        $this->base = \Manager::getConf('login.ldap.base');

        $this->conn = ldap_connect($this->host, $this->port);
        ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($this->conn, $this->user, $this->pass);
        if (!$r) {
            $this->printLdapError('Error on ldap connection!');
            exit;
        }

        mtrace('Abrindo conexao ao LDAP!');
        return true;
    }

    public function __destruct()
    {
        ldap_close($this->conn);
        mtrace('Fechando conexao com LDAP!');
    }

    /**
     * Retorna a instância da conexão que está sendo utilizada por essa classe.
     * @return resource
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Retorna o conjunto de atributos relativos ao DN especifico.
     * @param string $dn
     * @param array $attributes
     * @return array
     */
    public function getAttributes($dn, array $attributes = array())
    {
        $search = ldap_search($this->conn, $dn,
            'objectClass=*', $attributes);

        if (!$search) {
            return false;
        }

        return ldap_get_entries($this->conn, $search);
    }

    public function changeDN($dn, $newDn)
    {
        $decomposed = explode(',', $newDn);
        $dnPre = $decomposed[0];
        unset($decomposed[0]);
        $newParent = implode(',', $decomposed);

        $return = ldap_rename($this->conn, $dn, $dnPre, $newParent, true);

        if (!$return) {
            $this->printLdapError("Error changind DN: $dn ===> $newDn");
        }

        mtrace("[LDAP]DN $dn alterada para $newDn");
        return $return;
    }

    public function addOrModify($dn, $info)
    {
        if ($this->dnExists($dn)) {
            $this->modify($dn, $info);
        } else {
            $this->add($dn, $info);
        }
    }

    /**
     * Verifica se o Domain Name já existe na base.
     * @param string $dn
     * @return boolean
     */
    public function dnExists($dn)
    {
        $result = $this->getAttributes($dn);
        return $result ? $result['count'] > 0 : false;
    }

    /**
     * Adiciona um novo registro na base.
     * @param string $dn
     * @param array $info
     * @return boolean
     */
    public function add($dn, array $info)
    {
        $return = ldap_add($this->conn, $dn, $info);

        if (!$return) {
            $strInfo = serialize($info);
            $this->printLdapError("Error addind into LDAP. Info: $strInfo");
        }

        return $return;
    }

    /**
     * Modifica um registro na base
     * @param string $dn
     * @param array $info
     * @return bool
     */
    public function modify($dn, array $info)
    {
        $return = ldap_modify($this->conn, $dn, $info);

        if (!$return) {
            $this->printLdapError('Error on ldap modify!');
        }
        return $return;
    }

    /**
     * Retorna todos os registros que atendem ao filtro definido.
     *
     * @param string $filter
     * @param array $attributes
     * @return array
     */
    public function search($filter, array $attributes = array())
    {
        if (count($attributes) == 0) {
            $sr = ldap_search($this->conn, $this->base, $filter);
        } else {
            $sr = ldap_search($this->conn, $this->base, $filter, $attributes);
        }

        return ldap_get_entries($this->conn, $sr);
    }

    /**
     * Retorna o último erro LDAP
     *
     * @return string
     */
    public function getLastError()
    {
        return ldap_error($this->conn);
    }

    /**
     * Converte uma string MD5 para o formato utilizado no servidor LDAP.
     *
     * @param string $hexMD5
     * @return string
     */
    public static function md5ToLdap($hexMD5)
    {
        $return = '';
        foreach (str_split($hexMD5, 2) as $pair) {
            $return .= chr(hexdec($pair));
        }

        return '{MD5}' . base64_encode($return);
    }

    /**
     * Converte uma string utilizada no servidor LDAP para o formato MD5.
     * @param string $hexLdap
     * @return string
     */
    public static function ldapToMd5($hexLdap)
    {
        $return = '';

        $clean = base64_decode(preg_replace('/{MD5}/i', '', $hexLdap), false);
        foreach (str_split($clean, 1) as $char) {
            $ex = dechex(ord($char));
            if (strlen($ex) < 2) {
                $ex = "0$ex";
            }
            $return .= $ex;
        }
        return $return;
    }


    /**
     * Exibe uma mensagem com o último erro ocorrido no servidor LDAP.
     * @param string $msg
     */
    public function printLdapError($msg)
    {
        $errmsg = ldap_error($this->conn);
        $prompt = _M("$msg - $errmsg", 'Ldap');

        mtrace($prompt);
        print($prompt);
    }

}

