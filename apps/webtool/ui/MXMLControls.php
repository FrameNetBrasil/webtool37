<?php

/* Copyright [2011, 2012, 2013] da Universidade Federal de Juiz de Fora
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

class MXMLControls extends MBase
{

    public $root;
    public $context;
    public $path;
    public $painter;

    public function __construct()
    {
        parent::__construct();
        $this->painter = Manager::getPainter();
    }

    public function loadFile($xmlFile, $context = NULL)
    {
        libxml_use_internal_errors(true);
        $this->context = $context;
        $this->path = pathinfo($xmlFile, PATHINFO_DIRNAME);
        $xmlString = file_get_contents($xmlFile);
        $xmlString = utf8_encode($xmlString);
        $this->root = simplexml_load_string($xmlString, NULL, LIBXML_NOCDATA);
        if (!$this->root) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                $errorMsg .= $this->getErrors($error, $xmlString);
            }
            mtrace($errorMsg);
            libxml_clear_errors();
        }
    }

    public function loadString($xmlString, $context = NULL)
    {
        $this->root = simplexml_load_string(utf8_encode($xmlString));
        $this->context = $context;
    }

    public function fetch($xmlFile, $context = NULL)
    {
        $this->loadFile($xmlFile, $context);
        return $this->get();
    }

    public function get($nodeName = '')
    {
        $root = ($nodeName == '') ? $this->root : $this->root->$nodeName;
        return $this->getControls($root);
    }

    public function process($control = NULL, $nodeName = '')
    {
        $root = ($nodeName == '') ? $this->root : $this->root->$nodeName;
        $context = $control ? : $this->context;
        if (!$context) {
            throw new EControlException("MXmlControl::process - control is NULL.");
        }
        $this->getControlFromNode($context, $root);
    }

    private function getControls($node)
    {
        $controls = array();
        if ($node) {
            foreach ($node->children() as $fieldClass => $node) {
                if ($this->ignoreElement($node)) {
                    continue;
                }
                if (($fieldClass == 'css') || ($fieldClass == 'style')) {
                    $this->handleNodeCSS($node);
                } elseif ($fieldClass == 'javascript') {
                    $this->handleNodeJavascript($node);
                } elseif ($fieldClass == 'include') {
                    $this->getControlsFromInclude($node, $controls);
                } else {
                    $control = $this->getControlFromNode($fieldClass, $node);
                    if ($control != NULL) {
                        $controls[] = $control;
                    }
                }
            }
        }
        return $controls;
    }

    private function getControlsFromInclude($node, &$controls, $handleChildren = false)
    {
        if ($node) {
            if ($this->ignoreElement($node)) {
                return NULL;
            }
            $attributes = $node->attributes();
            if ($attributes['file']) {
                $fileName = $attributes['file'];
                $file = $this->path . '/' . $this->processValue($fileName);
            } elseif ($attributes['component']) {
                $fileName = $attributes['component'];
                $file = Manager::getAppPath('components/' . $fileName);
            }
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if ($extension == 'xml') {
                $xmlControls = new MXMLControls();
                $xmlControls->loadFile($file, $this->context);
                foreach ($xmlControls->get() as $c) {
                    $controls->addControl($c);
                }
            } elseif ($extension == 'html') {
                $control = new MBaseControl('mhtml');
                $control->tag = 'div';
                $template = new MTemplate(dirname($file));
                $template->multicontext([
                    'page' => Manager::getPage(),
                    'data' => Manager::getData(),
                    'template' => $template,
                    'painter' => Manager::getPainter()
                ]);
                $control->inner = $template->fetch($fileName);;
                $controls->addControl($control);
            } elseif ($extension == 'php') {
                include_once($file);
                $fileName = end(explode('/', $fileName)) ? : $fileName;
                $className = str_replace('.' . $extension, '', $fileName);
                $c = new $className;
                $this->getPropertiesFromNode($c, $node);
                if ($handleChildren) {
                    $controls->addControl($c);
                } else {
                    $controls[] = $c;
                }
            }
        }
    }

    private function getControlFromNode($fieldClass, $node)
    {
        if ($this->ignoreElement($node)) {
            return NULL;
        }
        if ($this->painter->hasMethod($fieldClass)) {
            $control = new $fieldClass();
        } else {
            $control = new MBaseControl('mhtml');
            $control->tag = $fieldClass;
        }
        $this->getPropertiesFromNode($control, $node);
        MUtil::setIfNull($control->property->name, $control->property->id);
        $this->handleChildren($control, $node);
        if ($attributes['base'] != '') {
            $file = $this->path . '/' . $attributes['base'] . '.xml';
            $baseControls = $this->fetch($file, $this->context);
            $first = $baseControls[0];
            $first->addControl($control);
            return $first;
        }
        return $control;
    }

    private function getPropertiesFromNode($control, $node)
    {
        $attributes = $node->attributes();
        foreach ($attributes as $property => $value) {
            $control->__set($property, $this->processValue($value, $control));
        }
        $cdata = trim(utf8_decode((string) $node[0]));
        if ($cdata) {
            $control->property->cdata = $cdata;
        }
    }

    private function handleNodeCSS($node)
    {
        foreach ($node->attributes() as $property => $value) {
            if ($property == 'file') {
                Manager::getPage()->addStyleSheet($this->processValue(trim(utf8_decode((string) $value))));
            } elseif ($property == 'code') {
                Manager::getPage()->addStyleSheetCode(trim(utf8_decode((string) $value)));
            }
        }
        $cdata = trim(utf8_decode((string) $node[0]));
        if ($cdata) {
            Manager::getPage()->addStyleSheetCode($cdata);
        }
    }

    private function handleNodeJavascript($node)
    {
        foreach ($node->attributes() as $property => $value) {
            $value = $this->processValue(trim(utf8_decode((string) $value)));
            if ($property == 'onload') {
                Manager::getPage()->onLoad($value);
            } elseif ($property == 'code') {
                Manager::getPage()->addJsCode($value);
            } elseif ($property == 'helper') {
                Manager::getPage()->onLoad($value);
            } elseif ($property == 'src') {
                Manager::getPage()->addScriptURL($value);
            } elseif ($property == 'file') {
                Manager::getPage()->addJsFile($value);
            }
        }
        $cdata = trim(utf8_decode((string) $node[0]));
        if ($cdata) {
            Manager::getPage()->addJsCode($this->processValue($cdata));
        }
    }

    private function handleChildren($control, $parent)
    {
        $context = $this->context;
        foreach ($parent->children() as $tag => $node) {
            $attributes = new \StdClass();
            foreach ($node->attributes() as $property => $value) {
                $attributes->$property = $this->processValue($value);
            }
            $method = "handleChildren" . $tag;
            if (method_exists($this, $method)) {
                $this->$method($control, $tag, $node, $attributes);
            } else {
                $method = 'add' . $tag;
                if (method_exists($control, $method)) {
                    $control->$method($attributes);
                } else {
                    $child = $this->getControlFromNode($tag, $node);
                    $control->addControl($child);
                }
            }
        }
    }

    private function handleChildrenEvent($control, $tag, $node, $attributes)
    {
        $control->addEvent($attributes);
    }

    private function handleChildrenAjax($control, $tag, $node, $attributes)
    {
        $attributes->target = $attributes->load ? : $attributes->target;
        $control->addAjax($attributes);
    }

    private function handleChildrenData($control, $tag, $node, $attributes)
    {
        if ($attributes->load) {
            $control->setData($attributes->load, $control);
        }
        if ($attributes->id) {
            $id = $attributes->id;
            $control->data->$id = $attributes->value;
        }
    }

    private function handleChildrenProperty($control, $tag, $node, $attributes)
    {
        foreach ($attributes as $property => $value) {
            $control->property->$property = $value;
        }
    }

    private function handleChildrenAttribute($control, $tag, $node, $attributes)
    {
        foreach ($attributes as $property => $value) {
            $control->property->$property = $value;
        }
    }

    private function handleChildrenStyle($control, $tag, $node, $attributes)
    {
        foreach ($attributes as $property => $value) {
            $control->style->$property = $value;
        }
    }

    private function handleChildrenOptions($control, $tag, $node, $attributes)
    {
        foreach ($attributes as $property => $value) {
            $control->options->$property = $value;
        }
    }

    private function handleChildrenInclude($control, $tag, $node, $attributes)
    {
        $this->getControlsFromInclude($node, $control, true);
    }

    private function handleChildrenAuth($control, $tag, $node, $attributes)
    {
        $perms = explode(':', $attributes->access);
        $right = Manager::getPerms()->getRight($perms[1]);
        if (Manager::checkAccess($perms[0], $right)) {
            $this->handleChildren($control, $node);
        }
    }

    private function handleChildrenMethod($control, $tag, $node, $attributes)
    {
        $func = create_function($attributes->args, trim(utf8_decode((string) $node[0])));
        $name = $attributes->name;
        $control->$name = $func;
    }

    private function handleChildrenCSS($control, $tag, $node, $attributes)
    {
        $this->handleNodeCSS($node);
    }

    private function handleChildrenJavascript($control, $tag, $node, $attributes)
    {
        $this->handleNodeJavascript($node);
    }

    private function processValue($value, $control = null)
    {
        $context = $this->context;
        $value = trim(utf8_decode((string) $value));
        if ($value == 'false') {
            $value = false;
        } else if ($value == 'true') {
            $value = true;
        } elseif ($value{0} == '$') {
            $value = str_replace("\$this", "\$context", $value);
            $value = eval('return ' . $value . ';');
        } elseif ($value{0} == '{') {
            if ($value{1} == '{') {
                $value = str_replace("\$this", "\$context", $value);
                $value = substr($value, 2, -2);
                $valueFunction = create_function('$context', 'return ' . $value . ';');
                $value = $valueFunction($context);
            }
        } elseif (strpos($value, '{{') !== false) {
            $template = str_replace("\$this", "\$context", $value);
            if (preg_match_all("/{{(.*?)}}/", $template, $m)) {
                foreach ($m[1] as $i => $expression) {
                    $valueFunction = create_function('$context', 'return ' . $expression . ';');
                    $value = $valueFunction($context);
                    $template = str_replace($m[0][$i], sprintf('%s', $value), $template);
                }
            }
            $value = $template;
        }
        return $value;
    }

    private function parse($str)
    {
        $callback = function($matches) {
            return $this->processValue($matches[0]);
        };
        return preg_replace_callback("/\{\{(.*)\}\}/", $callback, $str);
    }

    private function ignoreElement($element)
    {
        if ($element) {
            $attributes = $element->attributes();
            if (property_exists($attributes, 'process') && ($this->processValue($attributes->process) == false)) {
                return true;
            }
        }
        return false;
    }

    private function getErrors($error, $xmlString)
    {
        $xml = explode("\n", $xmlString);
        $return = $xml[$error->line - 1] . "\n";
        $return .= str_repeat('-', $error->column) . "^\n";
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "Warning $error->code: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "Fatal Error $error->code: ";
                break;
        }
        $return .= trim($error->message) .
                "\n  Line: $error->line" .
                "\n  Column: $error->column";
        if ($error->file) {
            $return .= "\n  File: $error->file";
        }
        return "$return\n\n--------------------------------------------\n\n";
    }

    /**
     * Conversão de entidades nomeadas HTML em entidades numéricas XML.
     * Adaptado de: https://gist.github.com/inanimatt/879249
     * @param string $text
     * @return string
     */
    private function convertHTMLEntities($string)
    {
        return preg_replace_callback(
                '/&([a-zA-Z][a-zA-Z0-9]+);/S',
                /* Swap HTML named entity with its numeric equivalent. If the entity
                 * isn't in the lookup table, this function returns a blank, which
                 * destroys the character in the output - this is probably the
                 * desired behaviour when producing XML. */ function ($matches) {
            static $table = array('quot' => '&#34;', 'amp' => '&#38;', 'lt' => '&#60;', 'gt' => '&#62;',
                'OElig' => '&#338;', 'oelig' => '&#339;', 'Scaron' => '&#352;', 'scaron' => '&#353;',
                'Yuml' => '&#376;', 'circ' => '&#710;', 'tilde' => '&#732;', 'ensp' => '&#8194;',
                'emsp' => '&#8195;', 'thinsp' => '&#8201;', 'zwnj' => '&#8204;', 'zwj' => '&#8205;',
                'lrm' => '&#8206;', 'rlm' => '&#8207;', 'ndash' => '&#8211;', 'mdash' => '&#8212;',
                'lsquo' => '&#8216;', 'rsquo' => '&#8217;', 'sbquo' => '&#8218;', 'ldquo' => '&#8220;',
                'rdquo' => '&#8221;', 'bdquo' => '&#8222;', 'dagger' => '&#8224;', 'Dagger' => '&#8225;',
                'permil' => '&#8240;', 'lsaquo' => '&#8249;', 'rsaquo' => '&#8250;', 'euro' => '&#8364;',
                'fnof' => '&#402;', 'Alpha' => '&#913;', 'Beta' => '&#914;', 'Gamma' => '&#915;',
                'Delta' => '&#916;', 'Epsilon' => '&#917;', 'Zeta' => '&#918;', 'Eta' => '&#919;',
                'Theta' => '&#920;', 'Iota' => '&#921;', 'Kappa' => '&#922;', 'Lambda' => '&#923;',
                'Mu' => '&#924;', 'Nu' => '&#925;', 'Xi' => '&#926;', 'Omicron' => '&#927;',
                'Pi' => '&#928;', 'Rho' => '&#929;', 'Sigma' => '&#931;', 'Tau' => '&#932;',
                'Upsilon' => '&#933;', 'Phi' => '&#934;', 'Chi' => '&#935;', 'Psi' => '&#936;',
                'Omega' => '&#937;', 'alpha' => '&#945;', 'beta' => '&#946;', 'gamma' => '&#947;',
                'delta' => '&#948;', 'epsilon' => '&#949;', 'zeta' => '&#950;', 'eta' => '&#951;',
                'theta' => '&#952;', 'iota' => '&#953;', 'kappa' => '&#954;', 'lambda' => '&#955;',
                'mu' => '&#956;', 'nu' => '&#957;', 'xi' => '&#958;', 'omicron' => '&#959;',
                'pi' => '&#960;', 'rho' => '&#961;', 'sigmaf' => '&#962;', 'sigma' => '&#963;',
                'tau' => '&#964;', 'upsilon' => '&#965;', 'phi' => '&#966;', 'chi' => '&#967;',
                'psi' => '&#968;', 'omega' => '&#969;', 'thetasym' => '&#977;', 'upsih' => '&#978;',
                'piv' => '&#982;', 'bull' => '&#8226;', 'hellip' => '&#8230;', 'prime' => '&#8242;',
                'Prime' => '&#8243;', 'oline' => '&#8254;', 'frasl' => '&#8260;', 'weierp' => '&#8472;',
                'image' => '&#8465;', 'real' => '&#8476;', 'trade' => '&#8482;', 'alefsym' => '&#8501;',
                'larr' => '&#8592;', 'uarr' => '&#8593;', 'rarr' => '&#8594;', 'darr' => '&#8595;',
                'harr' => '&#8596;', 'crarr' => '&#8629;', 'lArr' => '&#8656;', 'uArr' => '&#8657;',
                'rArr' => '&#8658;', 'dArr' => '&#8659;', 'hArr' => '&#8660;', 'forall' => '&#8704;',
                'part' => '&#8706;', 'exist' => '&#8707;', 'empty' => '&#8709;', 'nabla' => '&#8711;',
                'isin' => '&#8712;', 'notin' => '&#8713;', 'ni' => '&#8715;', 'prod' => '&#8719;',
                'sum' => '&#8721;', 'minus' => '&#8722;', 'lowast' => '&#8727;', 'radic' => '&#8730;',
                'prop' => '&#8733;', 'infin' => '&#8734;', 'ang' => '&#8736;', 'and' => '&#8743;',
                'or' => '&#8744;', 'cap' => '&#8745;', 'cup' => '&#8746;', 'int' => '&#8747;',
                'there4' => '&#8756;', 'sim' => '&#8764;', 'cong' => '&#8773;', 'asymp' => '&#8776;',
                'ne' => '&#8800;', 'equiv' => '&#8801;', 'le' => '&#8804;', 'ge' => '&#8805;',
                'sub' => '&#8834;', 'sup' => '&#8835;', 'nsub' => '&#8836;', 'sube' => '&#8838;',
                'supe' => '&#8839;', 'oplus' => '&#8853;', 'otimes' => '&#8855;', 'perp' => '&#8869;',
                'sdot' => '&#8901;', 'lceil' => '&#8968;', 'rceil' => '&#8969;', 'lfloor' => '&#8970;',
                'rfloor' => '&#8971;', 'lang' => '&#9001;', 'rang' => '&#9002;', 'loz' => '&#9674;',
                'spades' => '&#9824;', 'clubs' => '&#9827;', 'hearts' => '&#9829;', 'diams' => '&#9830;',
                'nbsp' => '&#160;', 'iexcl' => '&#161;', 'cent' => '&#162;', 'pound' => '&#163;',
                'curren' => '&#164;', 'yen' => '&#165;', 'brvbar' => '&#166;', 'sect' => '&#167;',
                'uml' => '&#168;', 'copy' => '&#169;', 'ordf' => '&#170;', 'laquo' => '&#171;',
                'not' => '&#172;', 'shy' => '&#173;', 'reg' => '&#174;', 'macr' => '&#175;',
                'deg' => '&#176;', 'plusmn' => '&#177;', 'sup2' => '&#178;', 'sup3' => '&#179;',
                'acute' => '&#180;', 'micro' => '&#181;', 'para' => '&#182;', 'middot' => '&#183;',
                'cedil' => '&#184;', 'sup1' => '&#185;', 'ordm' => '&#186;', 'raquo' => '&#187;',
                'frac14' => '&#188;', 'frac12' => '&#189;', 'frac34' => '&#190;', 'iquest' => '&#191;',
                'Agrave' => '&#192;', 'Aacute' => '&#193;', 'Acirc' => '&#194;', 'Atilde' => '&#195;',
                'Auml' => '&#196;', 'Aring' => '&#197;', 'AElig' => '&#198;', 'Ccedil' => '&#199;',
                'Egrave' => '&#200;', 'Eacute' => '&#201;', 'Ecirc' => '&#202;', 'Euml' => '&#203;',
                'Igrave' => '&#204;', 'Iacute' => '&#205;', 'Icirc' => '&#206;', 'Iuml' => '&#207;',
                'ETH' => '&#208;', 'Ntilde' => '&#209;', 'Ograve' => '&#210;', 'Oacute' => '&#211;',
                'Ocirc' => '&#212;', 'Otilde' => '&#213;', 'Ouml' => '&#214;', 'times' => '&#215;',
                'Oslash' => '&#216;', 'Ugrave' => '&#217;', 'Uacute' => '&#218;', 'Ucirc' => '&#219;',
                'Uuml' => '&#220;', 'Yacute' => '&#221;', 'THORN' => '&#222;', 'szlig' => '&#223;',
                'agrave' => '&#224;', 'aacute' => '&#225;', 'acirc' => '&#226;', 'atilde' => '&#227;',
                'auml' => '&#228;', 'aring' => '&#229;', 'aelig' => '&#230;', 'ccedil' => '&#231;',
                'egrave' => '&#232;', 'eacute' => '&#233;', 'ecirc' => '&#234;', 'euml' => '&#235;',
                'igrave' => '&#236;', 'iacute' => '&#237;', 'icirc' => '&#238;', 'iuml' => '&#239;',
                'eth' => '&#240;', 'ntilde' => '&#241;', 'ograve' => '&#242;', 'oacute' => '&#243;',
                'ocirc' => '&#244;', 'otilde' => '&#245;', 'ouml' => '&#246;', 'divide' => '&#247;',
                'oslash' => '&#248;', 'ugrave' => '&#249;', 'uacute' => '&#250;', 'ucirc' => '&#251;',
                'uuml' => '&#252;', 'yacute' => '&#253;', 'thorn' => '&#254;', 'yuml' => '&#255;'
            );
            // Entity not found? Destroy it.
            return isset($table[$matches[1]]) ? $table[$matches[1]] : '';
        }, $string);
    }

}
