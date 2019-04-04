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

class MBaseControl extends MBase
{

    /**
     * Código visual interno do controle
     */
    public $inner;

    /**
     * Código visual completo do controle.
     */
    public $result;

    /**
     * Eventos associados ao controle.
     * @var array
     */
    protected $event;

    /**
     * Estilos CSS associados ao controle.
     * @var object
     */
    public $style;

    /**
     * Options Javascript associados ao controle.
     * @var object
     */
    public $options;

    /**
     * Argumentos usados na construção do controle.
     * @var type 
     */
    protected $args;
    
    /**
     * Controles-filhos.
     * @var array
     */
    protected $controls;
    
    /**
     * Nome da tag HTML, no caso de controles MHTML.
     * @var string
     */
    public $tag;
    
    /**
     * Objeto MForm ao qual este controle pertence.
     * @var MForm
     */
    public $form;
    
    /**
     * Validadores associados a este controle.
     * @var type 
     */
    public $validators;

    function __construct()
    {
        parent::__construct();
        $numArgs = func_num_args();
        $className = get_class($this);
        $names = array(
            'namespace' => array_slice(explode('\\', $className), 0, -1),
            'classname' => strtolower(join('', array_slice(explode('\\', $className), -1)))
        );
        if ($numArgs == 0) {
            $this->className = $names['classname'];
            $this->args = array();
        } elseif ($numArgs == 1) {
            $arg0 = func_get_arg(0);
            if (is_string($arg0)) {
                $this->className = $arg0;
                $this->args = array();
            } else {
                $this->className = $names['classname'];
                $this->args = $arg0;
            }
        } elseif ($numArgs == 2) {
            $this->className = func_get_arg(0);
            $this->args = func_get_arg(1);
        }
        $this->onCreate();
    }

    public function __get($property)
    {
        $value = parent::__get($property);
        if ($value instanceof MNULL) {
            $selector = MStyle::selector($property);
            if ($selector != "") {
                $value = $this->style->$selector;
            } else {
                $value = $this->property->$property;
            }
        }
        return $value;
    }

    public function __set($property, $value)
    {
        $set = parent::__set($property, $value);
        if ($set instanceof MNULL) {
            $selector = MStyle::selector($property);
            if ($selector != "") {
                $this->style->$selector = $value;
            } else {
                $this->property->$property = $value;
            }
        }
    }

    /**
     * Método fábrica (factory method, usado para instanciar um controle com base no className.
     * @param string $className Nome da classe
     * @param string $path Path do arquivo XML que define a classe.
     * @return \Maestro\UI\MControl
     */
    public function instance($className, $path = '')
    {
        if (class_exists($className, true)) {
            $control = new $className();
        } else {
            $file = $path . '/' . $className . '.xml';
            if (file_exists($file)) {
                $controls = $this->getControlsFromXML($file);
                $control = array_shift($controls); // retorna o primeiro controle definido no arquivo xml
            } else {
                $control = new $className();
            }
        }
        if ($control) {
            if ($this->view) {
                $control->setView($this->view);
            }
        }
        return $control;
    }

    /**
     * Obtem a definição do controle via um arquivo XML.
     * @param string $file Nome do arquivo.
     */
    public function getControlsFromXML($file)
    {
        $xmlControls = new MXMLControls();
        $controls = $xmlControls->fetch($file, $this);
        $this->addControls($controls);
    }

    function onCreate()
    {
        $this->style = new \StdClass();
        $this->options = new \StdClass();
        $this->property->class = [];
        $this->inner = '';
        $this->event = [];
        $this->controls = [];
        $this->validators = [];
        $this->result = '';
        $this->inner = '';
        $this->property->id = $this->className . '_' . substr(uniqid('',TRUE), -6);
        if (count($this->args) > 0) {
            foreach ($this->args as $property => $value) {
                if ($property == 'tag') {
                    $this->$property = $value;
                } else {
                    $this->property->$property = $value;
                }
            }
        }
    }

    public function generate()
    {
        $this->onBeforeGenerate();
        $this->onGenerate();
        $this->onAfterGenerate();
        return $this->result;
    }

    function onBeforeGenerate()
    {
        
    }

    function onGenerate()
    {
        $this->render = $method = $this->className;
        $painter = $this->getPainter();
        $this->result = $painter->$method($this);
        $painter->generateEvents($this);
    }

    function onAfterGenerate()
    {
        
    }

    function regenerate()
    {
        $this->result = '';
        $this->generate();
    }
    
    /*
      Identification - name = id
     */

    public function setName($name)
    {
        MUtil::setIfNull($this->property->id, $name);
        $this->property->name = $name;
    }

    public function setId($id)
    {
        $this->property->id = $id;
        $this->property->name = $id;
    }

    public function getId()
    {
        return $this->property->id;
    }

    /*
      Facade to Style methods
     */

    public function addClass($cssClass)
    {
        $this->setClass($cssClass, TRUE);
    }

    public function setClass($cssClass, $add = true)
    {
        if ($add) {
            $this->property->class[$cssClass] = $cssClass;
        } else {
            $this->property->class = [$cssClass];
        }
    }

    public function hasClass($pattern)
    {
        $arClasses = implode(' ', $this->property->class);
        $classes = explode(' ', $arClasses);
        foreach ($classes as $class) {
            if (preg_match($pattern, trim($class)) == true) {
                return true;
            }
        }
        return false;
    }

    public function getClass()
    {
        return $this->property->class;
    }

    public function getClassStr()
    {
        return implode(' ', $this->property->class);
    }

    public function addStyle($name, $value)
    {
        $this->style->$name = $value;
    }

    public function cloneStyle(MBaseControl $control)
    {
        $this->style = $control->getStyle();
    }

    public function setStyle($style)
    {
        $this->style = $style;
    }

    public function getStyle()
    {
        return $this->style;
    }

    /*
      Events and Ajax
     */

    public function addEvent($objEvent)
    {
        $objEvent->id = $this->property->id;
        $this->event[$event->event][] = $objEvent;
    }

    public function hasEvent($event)
    {
        return (count($this->event[$event]) > 0);
    }

    public function clearEvent($event)
    {
        $this->event[$event] = array();
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function setEvent($event)
    {
        $this->event = $event;
    }

    public function ajaxText($event, $url, $updateElement, $preventDefault = false)
    {
        $objAjax = (object)[
            'type' => 'text',
            'event' => $event,
            'url' => $url,
            'load' => $updateElement,
            'preventDefault' => $preventDefault
        ];
        $this->addAjax($objAjax);
    }

    public function ajaxEvent($event, $url, $callback = null, $preventDefault = false)
    {
        $objAjax = (object)[
            'type' => 'json',
            'event' => $event,
            'url' => $url,
            'preventDefault' => $preventDefault
        ];
        $this->addAjax($objAjax);
    }

    public function addAjax($objAjax)
    {
        $url = Manager::getURL($objAjax->url);
        if ($objAjax->type == 'text') {
            $handler = "manager.doAjaxText('{$url}','{$objAjax->target}', '{$this->id}');";
        } else {
            $handler = "manager.doAjax('{$url}','{$objAjax->callback}', '{$this->id}');";
        }
        $objEvent = (object)[
            'event' => $objAjax->event,
            'handler' => $handler,
            'preventDefault' => (bool)$objAjax->preventDefault
        ];
        $this->addEvent($objEvent);
    }

    /*
     * Segurança
     */

    public function checkAccess()
    {
        $result = true;
        $access = $this->property->access;
        if ($access && Manager::isLogged()) {
            $perms = explode(':', $access);
            $right = Manager::getPerms()->getRight($perms[1]);
            $result = Manager::checkAccess($perms[0], $right);
        }
        return $result;
    }

    /*
     * Validators
     */

    public function addValidator($validator)
    {
        $this->validators[] = $validator;
    }

    /*
      Control as Container
     */

    public function setForm($form)
    {
        $this->form = $form;
        foreach ($this->controls as $child) {
            $child->setForm($form);
        }
    }

    public function addControls($controls)
    {
        if (!is_array($controls)) {
            $controls = array($controls);
        }
        foreach ($controls as $control) {
            $this->addControl($control);
        }
    }

    public function addControl($control, $pos = NULL)
    {
        if (!is_null($control)) {
            if (is_array($control)) {
                foreach ($control as $c) {
                    $this->_addControl($c);
                }
            } else if ($control instanceof MBaseControl) {
                $index = $control->property->id ? : uniqid('',TRUE);
                $this->controls[$index] = $control;
            } else {
                $this->controls[uniqid('',TRUE)] = $control;
            }
        }    
    }

    public function insertControl($control, $pos = 0)
    {
        $this->addControl($control);
    }

    public function setControl($control, $pos = 0)
    {
        $this->addControl($control);
    }

    public function setControls($controls)
    {
        if (is_array($controls)) {
            $this->clearControls();
            foreach ($controls as $c) {
                $this->addControl($c);
            }
        } else {
            $this->addControl($controls);
        }
    }

    public function getControls()
    {
        return $this->controls;
    }

    public function getControl($index)
    {
        return $this->controls[$index];
    }

    public function findControlById($id)
    {
        return $this->controls[$id];
    }

    public function clearControls()
    {
        $this->controls = [];
    }

    public function hasItems()
    {
        return count($this->controls) > 0;
    }

    public function findControl($id)
    {
        if ($this->property->id == $id) {
            return $this;
        }
        foreach ($this->controls as $control) {
            $result = $control->findControl($id);
            if ($result) {
                return $result;
            }
        }
    }

    /**
     * Atribui valores aos atributos do controle.
     *
     * @param $data (Object) Objeto pleno com os valores de atributos.
     * @param $control (MBaseControl Object) Controle que vai receber os dados.
     */
    public function setData($data, $control = null)
    {
        $current = ($control == null) ? $this : $control;
        if ($current->hasItems()) { // é um container: chamada recursiva
            foreach ($current->controls as $control) {
                $this->setData($data, $control);
            }
        } else {
            $name = $control->property->name;
            if ($name) {
                if (strpos($name, '::') !== false) {
                    list($obj, $name) = explode('::', $name);
                    $rawValue = $data->{$obj}->{$name};
                } elseif (strpos($name, '_') !== false) {
                    list($obj, $name) = explode('_', $name);
                    $rawValue = $data->{$obj}->{$name} ?: $data->$name;
                } else {
                    $rawValue = $data->$name;
                }
                if (isset($rawValue)) {
                    if ($rawValue instanceof MCurrency) {
                        $value = $rawValue->getValue();
                    } else if ($rawValue instanceof MCPF) {
                        $value = $rawValue->getPlainValue();
                    } else if ($rawValue instanceof MCNPJ) {
                        $value = $rawValue->getPlainValue();
                    } elseif (($rawValue instanceof MDate) || ($rawValue instanceof MTimestamp)) {
                        $value = $rawValue->format();
                    } else {
                        $value = $rawValue;
                    }
                    $control->property->value = $value;
                }
            }
        }
    }

    /*
      Content and rendering
     */

    public function __toString()
    {
        return $this->generate();
    }

}