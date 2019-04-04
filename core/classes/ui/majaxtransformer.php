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

class MAjaxTransformer
{

    static public function toString($node)
    {
        $returnValue = '';
        foreach ($node->composites as $composite) {
            $returnValue .= MAjaxTransformer::toString($composite);
        }
        $returnValue .= MAjaxTransformer::encode($node->getData(), $node->getEncoding());
        return $returnValue;
    }

    static public function toXML($node)
    {
        $returnValue = '<' . $node->getName();
        // handle attributes
        foreach ($node->attributes as $name => $value) {
            if ($value != '') {
                $returnValue .= ' ' . $name . '="' . $node->getAttribute($name) . '"';
            }
        }
        $returnValue .= '>';
        // handle subnodes
        foreach ($node->composites as $composite) {
            $returnValue .= MAjaxTransformer::toXML($composite);
        }
        $returnValue .= MAjaxTransformer::encode($node->getData(), $node->getEncoding())
            . '</' . $node->get_name() . '>';

        return $returnValue;
    }

    static public function toJSON($node)
    {
        $returnValue = '';
        $JSON_node = new \stdClass();
        // handle subnodes
        foreach ($node->composites as $composite) {
            if (!is_array($JSON_node->{$composite->nodeName})) {
                $JSON_node->{$composite->nodeName} = array();
            }
            $JSON_node->{$composite->nodeName}[] = $composite->nodes;
        }
        if ($id = $node->getId()) {
            $JSON_node->id = $id;
        }
        if ($type = $node->getType()) {
            $JSON_node->type = $type;
        }
        if ($data = $node->getData()) {
            $JSON_node->data = $data;
        }
        $returnValue = MJSON::encode($JSON_node);
        return $returnValue;
    }

    static public function detectUTF8($string)
    {
        return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
        )+%xs', $string);
    }

    static public function encode($data, $encoding)
    {
        if (MAjaxTransformer::detectUTF8($data)) {
            // if UTF-8 data was supplied everything is fine!
            $returnValue = $data;
        } elseif (function_exists('iconv')) {
            // iconv is by far the most flexible approach, try this first
            $returnValue = iconv($encoding, 'UTF-8', $data);
        } elseif ($encoding == 'ISO-8859-1') {
            // for ISO-8859-1 we can use utf8-encode()
            $returnValue = utf8_encode($data);
        } else {
            // give up. if UTF-8 data was supplied everything is fine!
            $returnValue = $data;
        } /* end: if */

        return $returnValue;
    }

    static public function decode($data, $encoding)
    {
        // convert string

        if (is_string($data)) {
            if (!MAjaxTransformer::detectUTF8($data)) {
                $returnValue = $data;
            } elseif (function_exists('iconv')) {
                // iconv is by far the most flexible approach, try this first
                $returnValue = iconv('UTF-8', $encoding, $data);
            } elseif ($encoding == 'ISO-8859-1') {
                // for ISO-8859-1 we can use utf8-decode()
                $returnValue = utf8_decode($data);
            } else {
                // give up. if data was supplied in the correct format everything is fine!
                $returnValue = $data;
            } // end: if
        } else {
            // non-string value
            $returnValue = $data;
        } // end: if

        return $returnValue;
    }

    /**
     * decodes a (nested) array of data from UTF-8 into the configured character set
     *
     * @access   public
     * @param    array $data data to convert
     * @param    string $encoding character encoding
     * @return   array
     */
    static public function decodeArray($data, $encoding)
    {
        $returnValue = array();

        foreach ($data as $key => $value) {

            if (!is_array($value)) {
                $returnValue[$key] = MAjaxTransformer::decode($value, $encoding);
            } else {
                $returnValue[$key] = MAjaxTransformer::decode_array($value, $encoding);
            }
        }

        return $returnValue;
    }

    /**
     * Determina o conjunto de caracters da saída, baseado no conjunto de caracteres da entrada.
     *
     * @param    string $encoding character encoding
     * @return   string
     */
    static public function findOutputCharset($encoding)
    {
        $returnValue = 'UTF-8';
        if (function_exists('iconv') || $encoding == 'UTF-8' || $encoding == 'ISO-8859-1') {

            $returnValue = 'UTF-8';
        } else {
            $returnValue = $encoding;
        }
        return $returnValue;
    }

}
