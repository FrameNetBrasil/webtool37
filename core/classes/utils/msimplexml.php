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
class MSimpleXml
{
    /**
     * Attribute Description.
     */
    public $xml;

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $file (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function __construct($file)
    {
        $this->xml = simplexml_load_file($file);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $node (tipo) desc
     * @param &$array (tipo) desc
     * @param $k =') (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    private function _ToSimpleArray($node, &$array = array(), $k = '')
    {
        foreach ((array)$node as $key => $var) {
            $aKey = ($k != '') ? $k . '.' . $key : $key;

            if (is_object($var)) {
                if (count((array)$var) == 0) {
                    $array[$aKey] = '';
                } else {
                    $this->_ToSimpleArray($var, $array, $aKey);
                }
            } elseif (is_array($var)) {
                $array[$aKey] = $var;
            } else {
                $array[$aKey] = utf8_decode((string)$var);
            }
        }
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param &$array ) (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function toSimpleArray(&$array = array(), $node = NULL)
    {
        if ($node == NULL)
            $node = $this->xml;

        $this->_ToSimpleArray($node, $array);
        return $array;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param &$array ) (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    private function _ToArray($xml)
    {
        if (get_class($xml) == 'SimpleXMLElement') {
            $attributes = $xml->attributes();

            foreach ($attributes as $k => $v) {
                if ($v)
                    $a[$k] = (string)$v;
            }

            $x = $xml;
            $xml = get_object_vars($xml);
        }

        if (is_array($xml)) {
            if (count($xml) == 0)
                return (string)$x; // for CDATA

            foreach ($xml as $key => $value) {
                $r[$key] = $this->_ToArray($value);
            }

            if (isset($a))
                $r['@'] = $a; // Attributes

            return $r;
        }

        return utf8_decode((string)$xml);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param &$array ) (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function toArray(&$array = array(), $node = NULL)
    {
        if ($node == NULL)
            $node = $this->xml;

        return $this->_ToArray($node);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $argument (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function XPath($argument)
    {
        return $this->xml->xpath($argument);
    }
}
