<?php

namespace Mesh\Element\Node;

use Mesh\Infra\Base;

class Node extends Base
{
    public $id;
    public $name;
    public $fullName; // no caso de pools: feature.pool
    public $type;
    public $logic; // 'A': matrix, 'O': attribute, 'C':constraint, 'R':relation, 'S': switch/relay, 'N': projection
    public $class;
    public $category; // atom, duo, comp
    public $idHead; // head na relação de dependência da UD
    public $wordIndex; // index da word no parser UD
    public $region;
    public $h; // #num do head da UD
    public $d; # num do dep da UD

    function __construct($id = '')
    {
        $this->id = $id;
        $this->name = $this->fullName = $this->id;
        $this->type = 'none';
        $this->logic = 'O';
        $this->class = '';
        $this->category = '';
        $this->idHead = -1;
        $this->wordIndex = -1;
        $this->h = -1;
        $this->d = -1;
    }

    public function setParams($params)
    {
        foreach ($params as $param => $value) {
            if (!is_object($value)) {
                $this->$param = $value;
            }
        }
    }

    public function setId($value) {
        $this->id = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setFullName($name)
    {
        $this->fullName = $name;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function setType($type)
    {
        $this->type = $type ?: 'none';
    }

    public function getType()
    {
        return $this->type;
    }

    public function setLogic($value)
    {
        $this->logic = $value;
    }

    public function getLogic()
    {
        return $this->logic;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getCategory()
    {
        return $this->category;
    }

}

