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

//
// md5crypt
// Action: Creates MD5 encrypted password
//
// Adapted from PostfixAdmin

class MMD5Crypt
{
    private $MAGIC = '$1$';
    private $ITOA64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    public function crypt($pw, $salt = "", $magic = "")
    {
        if ($magic == "") $magic = $this->MAGIC;
        if ($salt == "") $salt = $this->create_salt();
        $slist = explode("$", $salt);
        if ($slist[0] == "1") $salt = $slist[1];

        $salt = substr($salt, 0, 8);
        $ctx = $pw . $magic . $salt;
        $final = $this->hex2bin(md5($pw . $salt . $pw));

        for ($i = strlen($pw); $i > 0; $i -= 16) {
            if ($i > 16) {
                $ctx .= substr($final, 0, 16);
            } else {
                $ctx .= substr($final, 0, $i);
            }
        }
        $i = strlen($pw);

        while ($i > 0) {
            if ($i & 1) $ctx .= chr(0);
            else $ctx .= $pw[0];
            $i = $i >> 1;
        }
        $final = $this->hex2bin(md5($ctx));

        for ($i = 0; $i < 1000; $i++) {
            $ctx1 = "";
            if ($i & 1) {
                $ctx1 .= $pw;
            } else {
                $ctx1 .= substr($final, 0, 16);
            }
            if ($i % 3) $ctx1 .= $salt;
            if ($i % 7) $ctx1 .= $pw;
            if ($i & 1) {
                $ctx1 .= substr($final, 0, 16);
            } else {
                $ctx1 .= $pw;
            }
            $final = $this->hex2bin(md5($ctx1));
        }
        $passwd = "";
        $passwd .= $this->to64(((ord($final[0]) << 16) | (ord($final[6]) << 8) | (ord($final[12]))), 4);
        $passwd .= $this->to64(((ord($final[1]) << 16) | (ord($final[7]) << 8) | (ord($final[13]))), 4);
        $passwd .= $this->to64(((ord($final[2]) << 16) | (ord($final[8]) << 8) | (ord($final[14]))), 4);
        $passwd .= $this->to64(((ord($final[3]) << 16) | (ord($final[9]) << 8) | (ord($final[15]))), 4);
        $passwd .= $this->to64(((ord($final[4]) << 16) | (ord($final[10]) << 8) | (ord($final[5]))), 4);
        $passwd .= $this->to64(ord($final[11]), 2);
        return "$magic$salt\$$passwd";
    }

    public function create_salt()
    {
        srand((double)microtime() * 1000000);
        $salt = substr(md5(rand(0, 9999999)), 0, 8);
        return $salt;
    }

    public function hex2bin($str)
    {
        $len = strlen($str);
        $nstr = "";
        for ($i = 0; $i < $len; $i += 2) {
            $num = sscanf(substr($str, $i, 2), "%x");
            $nstr .= chr($num[0]);
        }
        return $nstr;
    }

    public function to64($v, $n)
    {
        $ret = "";
        while (($n - 1) >= 0) {
            $n--;
            $ret .= $this->ITOA64[$v & 0x3f];
            $v = $v >> 6;
        }
        return $ret;
    }
}
