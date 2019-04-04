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
class MXMLTree
{
    /**
     * Attribute Description.
     */
    public $stack;

    /**
     * Attribute Description.
     */
    public $top = 0;

    /**
     * Attribute Description.
     */
    public $tree;


    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $file ' (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function __construct($file = '', $options = 0)
    {
        $this->tree = null;
        $this->stack = array(); // this keeps track of what tag level you're at
        $doc = new domDocument();
        $doc->preserveWhiteSpace = false;
        $doc->load($file, $options);
        $root = $doc->documentElement;
        $this->parse($doc);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $node (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function push($node)
    {
        $this->stack[++$this->top] = $node;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public function pop()
    {
        return $this->stack[$this->top--];
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public function top()
    {
        return $this->stack[$this->top];
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public function count()
    {
        return $this->top;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $index (tipo) desc
     * @param $value (tipo) desc
     * @param $key (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function addProperty($index, $value, $key = NULL)
    {

        $v = ($key != NULL) ? array($key => $value) : $value;
        if (($p = $this->stack[$this->top]->properties[$index]) != NULL) {
            $p = is_array($p) ? $p : array($p);
            $v = is_array($v) ? $v : array($v);
            $this->stack[$this->top]->properties[$index] = array_merge($p, $v);
        } else {
            $this->stack[$this->top]->properties[$index] = $v;
        }
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $index (tipo) desc
     * @param &$value (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function addPropertyArray($index, &$value)
    {
        $p = $this->stack[$this->top]->properties[$index];
        if (isset($p)) {
            if (!is_array($p)) {
                $p = array($p);
            }
            $this->stack[$this->top]->properties[$index] = array_merge($p, array($value));
        } else
            $this->stack[$this->top]->properties[$index] = array($value);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $attr (tipo) desc
     * @param $value (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function addAttribute($attr, $value)
    {
        $this->stack[$this->top]->$attr = $value;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $data (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function setValue($data)
    {
        $this->stack[$this->top]->value = $data;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $name (tipo) desc
     * @param $attrs (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function _startElement($name, $attrs)
    {
        $node = new MXMLNode;
        $node->tag = $name;
        $node->attrs = $attrs;
        $this->push($node);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $name (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function _endElement($name)
    {
        $node = $this->pop();
        // Is there a parent node?
        if ($this->count()) {
            /**
             * Brief Class Description.
             * Complete Class Description.
             */
            $node->class = isset($node->attrs['class']) ? $node->attrs['class'] : $node->class;
            $name = isset($node->attrs['name']) ? $node->attrs['name'] : $node->tag;
            $type = isset($node->attrs['type']) ? $node->attrs['type'] : '';
            $key = isset($node->attrs['key']) ? $node->attrs['key'] : NULL;
            if ($node->tag == 'class')
                $this->addAttribute('class', $node->value);
            elseif (!count($node->properties)) {
                if ($node->value == 'true')
                    $node->value = TRUE;
                elseif ($node->value == 'false')
                    $node->value = FALSE;
                $this->addProperty($name, $node->value, $key);
            } elseif (($node->class == 'array') || ($type == 'array')) {
                $this->addProperty($name, $node->properties['item'], $key);
            } elseif ($node->class == 'associative') {
                $this->addProperty($name, $node->properties);
            } else {
                $this->addProperty($name, $node, $key);
            }
        } else $this->tree = $node;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $data (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function _characterData($data)
    {
        $this->setValue($data);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $domNode (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function parse($domNode)
    {
        if ($childDomNode = $domNode->firstChild) {
            static $depth = 0;
            while ($childDomNode) {
                if ($childDomNode->nodeType == XML_TEXT_NODE) {
                    $this->_characterData(utf8_decode(trim($childDomNode->nodeValue)));
                } elseif ($childDomNode->nodeType == XML_ELEMENT_NODE) {
                    $hasTag = 1;
                    $attrs = array();
                    if ($childDomNode->hasAttributes()) {
                        $array = $childDomNode->attributes;
                        foreach ($array AS $domAttribute) {
                            $attrs[$domAttribute->name] = $domAttribute->value;
                        }
                    }
                    $this->_startElement(utf8_decode($childDomNode->nodeName), $attrs);

                    if ($childDomNode->hasChildNodes()) {
                        $depth++;
                        $this->parse($childDomNode);
                        $depth--;
                    }
                    $this->_endElement(utf8_decode($childDomNode->nodeName));
                }
                $childDomNode = $childDomNode->nextSibling;
            }
            return $hasTag;
        }
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $node (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function getObject($node = NULL)
    {
        if ($node == NULL) {
            $node = $this->tree;
        }
        if (is_object($node)) {
            /**
             * Brief Class Description.
             * Complete Class Description.
             */
            $class = $node->class;
            $obj = new $class;
            foreach ($node->properties as $k => $v) {
                $obj->$k = $this->getObject($v);
            }
        } elseif (is_array($node)) {
            foreach ($node as $k => $v) {
                $obj[] = $this->getObject($v);
            }
        } else {
            $obj = $node;
        }
        return $obj;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $node (tipo) desc
     * @param $prefixClass =' (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function getObjectTree($node = NULL, $prefixClass = '')
    {
        if ($node == NULL) {
            $node = $this->tree;
        }
        if (is_object($node)) {
            /**
             * Brief Class Description.
             * Complete Class Description.
             */
            $class = $prefixClass . ($node->class != '' ? $node->class : $node->tag);
            $obj = new $class;
            foreach ($node->attrs as $k => $v) {
                $obj->$k = $v;
            }
            foreach ($node->properties as $k => $v) {
                $obj->$k = $this->getObjectTree($v, $prefixClass);
            }
        } elseif (is_array($node)) {
            foreach ($node as $k => $v) {
                $obj[] = $this->getObjectTree($v, $prefixClass);
            }
        } else {
            $obj = $node;
        }
        return $obj;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $node (tipo) desc
     * @param $prefixClass =' (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function getInterfaceTree($node = NULL, $prefixClass = '')
    {
        if ($node == NULL) {
            $node = $this->tree;
        }
        if (is_object($node)) {
            $class = $prefixClass . ($node->class != '' ? $node->class : $node->tag);
            $obj = new $class;
            foreach ($node->attrs as $k => $v) {
                $obj->$k = $v;
            }
            foreach ($node->properties as $k => $v) {
                if (is_object($v)) {
                    $obj->{$k}[] = $this->getInterfaceTree($v, $prefixClass);
                } else {
                    $obj->$k = $this->getInterfaceTree($v, $prefixClass);
                }

            }
        } elseif (is_array($node)) {
            foreach ($node as $k => $v) {
                $obj[] = $this->getInterfaceTree($v, $prefixClass);
            }
        } else {
            $obj = $node;
        }
        return $obj;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $node (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function getXMLTreeElement($node = null)
    {
        if (is_object($node)) {
            $obj = new MXMLTreeElement;

            foreach ($node->attrs as $k => $v) {
                $obj->$k = $v;
            }

            foreach ($node->properties as $k => $v) {
                $obj->$k = $this->getXMLTreeElement($v);
            }
        } elseif (is_array($node)) {
            foreach ($node as $k => $v) {
                $obj[] = $this->getXMLTreeElement($v);
            }
        } else {
            $obj = $node;
        }
        return $obj;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $node (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function getXMLTreeAsTreeArray($node = null)
    {
        if (is_object($node)) {
            $obj = array();
            foreach ($node->properties as $k => $v) {
                $obj[$k] = $this->getXMLTreeAsTreeArray($v);
            }
        } elseif (is_array($node)) {
            foreach ($node as $k => $v) {
                $obj[$k][] = $this->getXMLTreeAsTreeArray($v);
            }
        } else {
            $obj = $node;
        }
        return $obj;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $node (tipo) desc
     * @param &$=array (tipo) desc
     * @param $key =') (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function getXMLTreeAsFlatArray($node = null, &$array = array(), $key = '')
    {
        if (is_object($node)) {
            foreach ($node->properties as $k => $v) {
                $kk = $key . ($key != '' ? '.' : '') . $k;
                $this->getXMLTreeAsFlatArray($v, $array, $kk);
            }
        } elseif (is_array($node)) {
            foreach ($node as $k => $v) {
                $kk = $key . ($key != '' ? '.' : '') . $k;
                $this->getXMLTreeAsFlatArray($v, $array, $kk);
            }
        } else {
            $array[$key] = $node;
        }
        return $array;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public function getXMLTreeAsArray()
    {
        $array = array();
        $xml = $this->getXMLTreeElement($this->tree);
        $one = true;
        foreach ($xml as $attribute) {
            if (is_array($attribute) && $one) {
                $i = 0;
                foreach ($attribute as $element) {
                    $j = 0;
                    foreach ($element as $value) {
                        $array[$i][$j++] = $value;
                    }
                    $i++;
                }
            }
        }
        return $array;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public function getXMLTreeAsAssocArray()
    {
        $array = array();
        $xml = $this->getXMLTreeElement($this->tree);
        $one = true;
        foreach ($xml as $attribute) {
            if (is_array($attribute) && $one) {
                $i = 0;
                foreach ($attribute as $element) {
                    foreach ($element as $a => $value) {
                        $array[$i][$a] = $value;
                    }
                    $i++;
                }
            }
        }
        return $array;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $index (tipo) desc
     * @param $value (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function getXMLTreeAsValueArray($index, $value)
    {
        $array = array();
        $xml = $this->getXMLTreeElement($this->tree);
        $one = true;
        foreach ($xml as $attribute) {
            if (is_array($attribute) && $one) {
                foreach ($attribute as $element) {
                    $array[$element->$index] = $element->$value;
                }
            }
        }
        return $array;
    }
} 
