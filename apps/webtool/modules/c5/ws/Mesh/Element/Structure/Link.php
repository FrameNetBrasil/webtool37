<?php
namespace Mesh\Element\Structure;

use Mesh\Infra\Base;

class Link extends Base
{

    public static $staticId = 0;
    public $id;
    public $idSource;
    public $idTarget;
    public $optional;
    public $label;
    public $type;
    public $inhibitory;
    public $head;
    public $multiple;

    function __construct($idSource, $idTarget, $params)
    {
        $this->id = ++self::$staticId;
        $this->optional = (boolean)($params->optional == '1');
        $this->head = (boolean)($params->head == '1');
        $this->multiple = 9;//$params->multiple;
        $this->inhibitory = false;// ($params->inhibitory === true) ? true : false;
        $this->type = isset($params->type) ? $params->type : '';
        $this->system = ($this->type == 'semantic') ? 'R' : 'S';
        $this->label = $params->label ?: $params->relation;
        $this->idSource = $idSource;
        $this->idTarget = $idTarget;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdSource()
    {
        return $this->idSource;
    }

    public function getIdTarget()
    {
        return $this->idTarget;
    }

    public function isOptional()
    {
        return $this->optional;
    }

    public function isHead()
    {
        return $this->head;
    }

    public function setOptional($optional)
    {
        $this->optional = $optional;
    }

    public function isInhibitory()
    {
        return $this->inhibitory;
    }

    public function setInhibitory($inhibitory)
    {
        $this->inhibitory = $inhibitory;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getMultiple()
    {
        return $this->multiple;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setSystem($value)
    {
        $this->system = $value ?: 'S';
    }

    public function getSystem()
    {
        return $this->system;
    }

}

