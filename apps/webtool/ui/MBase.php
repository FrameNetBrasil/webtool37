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

class MBase
{

    /**
     * Nome da classe PHP do componente.
     */
    public $className;

    /**
     * Objeto que armazena as propriedades do componente.
     */
    public $property;

    /**
     * View que contém este componente.
     */
    public $view;

    /**
     * Dados associados ao componente (provenientes da view).
     */
    public $data;

    /**
     * Método da classe Painter usado para renderizar o componente
     */
    public $render;

    public function __construct($name = NULL)
    {
        $this->property = new \StdClass();
        $this->className = strtolower(get_class($this));
        $this->property->name = $name;
        $this->data = Manager::getData();
    }

    public function __get($property)
    {
        if (method_exists($this, ($method = 'get' . $property))) {
            return $this->$method();
        } else {
            return MNULL::getInstance();
        }
    }

    public function __set($property, $value)
    {
        if (method_exists($this, ($method = 'set' . $property))) {
            $this->$method($value);
            return $value;
        } else {
            return MNULL::getInstance();
        }
    }

    function __call($name, $args)
    {
        if (isset($this->$name)) {
            $args[] = $this;
            call_user_func_array($this->$name, $args);
        } else {
            throw new EControlException("Método {$name} não definido no controle {$this->id} ({$this->property->className})!");
        }
    }

    /**
     * The clone method.
     * It is used to clone controls, avoiding references to same attributes, styles and controls.
     */
    public function __clone()
    {
        $this->property = clone $this->property;
    }

    public function getProperties()
    {
        return $this->property;
    }

    public function setClassName($name)
    {
        $this->className = $name;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function setName($name)
    {
        $this->property->name = $name;
    }

    public function getName()
    {
        return $this->property->name;
    }

    public function getUI()
    {
        return Manager::getUI();
    }

    public function getPage()
    {
        return Manager::getPage();
    }

    public function setRender($value)
    {
        $this->render = $value;
    }

    public function getRender()
    {
        return $this->render;
    }

    public function getPainter()
    {
        return Manager::getPainter();
    }

    public function setView(MView $view)
    {
        $this->view = $view;
    }

    public function getView()
    {
        return $this->view ? : NULL;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getController()
    {
        return ($this->view ? $this->view->controller : NULL);
    }

    public function getService($service, $module = '')
    {
        $service = Manager::getService(Manager::getApp(), ($module == '' ? Manager::getModule() : $module), $service);
        $service->setData();
        return $service;
    }

}
