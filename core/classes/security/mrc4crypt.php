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

// Adapted from Javascript code at http://shop-js.sourceforge.net
// Created On: June 20, 2003

/**
 * Brief Class Description.
 * Complete Class Description.
 */
class MRC4Crypt
{
    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $key (tipo) desc
     * @param $text (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function rc4($key, $text)
    {
        $s = array
        ();

        for ($i = 0; $i < 256; $i++) {
            $s[$i] = $i;
        }

        $key_length = strlen($key);
        $y = 0;

        for ($x = 0; $x < 256; $x++) {
            $c = ord(substr($key, ($x % $key_length), 1));
            $y = ($c + $s[$x] + $y) % 256;
            $temp_swap = $s[$x];
            $s[$x] = $s[$y];
            $s[$y] = $temp_swap;
        }

        $cipher = "";
        $x = 0;
        $y = 0;
        $z = "";

        for ($x = 0; $x < strlen($text); $x++) {
            $x2 = $x % 256;
            $y = ($s[$x2] + $y) % 256;
            $temp = $s[$x2];
            $s[$x2] = $s[$y];
            $s[$y] = $temp;
            $z = $s[(($s[$x2] + $s[$y]) % 256)];
            $cipherby = ord(substr($text, $x, 1)) ^ $z;
            $cipher .= chr($cipherby);
        }

        return $cipher;
    }
}
