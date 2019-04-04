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
 * Brief Class Description.
 * Complete Class Description.
 */
class MQuotedPrintable
{

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $str (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function encode($str)
    {
        define('CRLF', "\r\n");
        $lines = preg_split("/\r?\n/", $str);
        $out = '';

        foreach ($lines as $line) {

            $newpara = '';

            for ($j = 0; $j <= strlen($line) - 1; $j++) {
                $char = substr($line, $j, 1);
                $ascii = ord($char);

                if ($ascii < 32 || $ascii == 61 || $ascii > 126) {
                    $char = '=' . strtoupper(dechex($ascii));
                }

                if ((strlen($newpara) + strlen($char)) >= 76) {
                    $out .= $newpara . '=' . CRLF;
                    $newpara = '';
                }
                $newpara .= $char;
            }
            $out .= $newpara . $char;
        }
        return trim($out);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $str (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function decode($str)
    {
        $out = preg_replace('/=\r?\n/', '', $str);
        $out = preg_replace('/=([A-F0-9]{2})/e', chr(hexdec('\\1')), $out);

        return trim($out);
    }

}
