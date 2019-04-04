<?php

namespace C5\Service;

use C5\Network\FullNetwork;

class Resource extends \Mesh\Infra\Base
{
    protected $avms;
    protected $valueNodes;
    protected $typeNetwork;
    protected $fullNetwork;
    protected $relays;

    public function __construct()
    {
        parent::__construct();
        $this->avms = (object)[
            'fn' => (object)[],
            'valence' => (object)[],
            'grammar' => (object)[]
        ];
        $this->valueNodes = [];
        $this->relays = [];
    }

    public function setTypeNetwork($typeNetwork)
    {
        $this->typeNetwork = $typeNetwork;
    }

    public function setFullNetwork(FullNetwork $fullNetwork)
    {
        $this->fullNetwork = $fullNetwork;
    }

    public function initAVM()
    {
        $this->valueNodes = [];
        $this->relays = [];
    }

    public function AVMAttributeValue($network, $avmAttr, $value)
    {
        if (is_array($value)) {
            $avmAttr->value = [];
            foreach ($value as $val) {
                $avmAttr->value[] = $val;
            }
        }
    }


    /*
     * Link $values to $attribute, using a relay as intermediate node
     * $values array com cada value do attribute
     * $attribute typeNode do attribute
     */
    public function linkValuesByRelay($values, $attribute)
    {
        $i = 0;
        $valueNodes = [];
        foreach ($values as $valueName) {
            if (isset($this->valueNodes[$valueName])) {
                $valueNodes = $valueNodes + $this->valueNodes[$valueName];
            } else {
                $node = $this->typeNetwork->getNode($valueName);
                if ($node == '') {
                    if (strpos($valueName, '.') !== false) {
                        //list($feature, $pool) = explode('.', $valueName);
                        $node = $this->typeNetwork->getNodeByFullName($valueName);
                        $valueNodes[] = $node;
                        $this->valueNodes[$valueName][] = $node;
                    } else {
                        $idNodes = $this->typeNetwork->getIdNodesByClass($valueName);
                        foreach ($idNodes as $idNode) {
                            $node = $this->typeNetwork->getNode($idNode);
                            $valueNodes[] = $node;
                            $this->valueNodes[$valueName][] = $node;
                        }
                    }
                } else {
                    $valueNodes[] = $node;
                    $this->valueNodes[$valueName][] = $node;
                }
            }
        }
        foreach ($valueNodes as $value) {
            $this->linkNodesByRelay($value, $attribute, 'rel_value');
        }
    }

    public function linkNodesByRelay($nodeSource, $nodeTarget, $relationLabel = 'rel_value')
    {
        $idSource = $nodeSource->getId();
        $idTarget = $nodeTarget->getId();
        $this->dump('---- ' . $relationLabel . '  from ' . $nodeSource->getName() . ' to ' . $nodeTarget->getName());
        $idNodeRelay = $this->getIdFor($idSource . $idTarget . uniqid());
        $relay = $this->typeNetwork->getOrCreateNode($idNodeRelay, (object)[
            'logic' => 'S',
            'name' => 'relay',
            'type' => 'relay',
            'region' => $nodeTarget->region
        ]);
        $this->relays[$idTarget][$idSource] = $relay;
        //$link = $this->typeNetwork->getLink($nodeSource, $nodeTarget);
        $this->typeNetwork->createLink($nodeSource, $relay, (object)[
            'optional' => $nodeTarget->optional,
            'label' => $relationLabel,
            'head' => true
        ]);
        $this->typeNetwork->createLink($relay, $nodeTarget, (object)[
            'optional' => $nodeTarget->optional,
            'label' => $relationLabel,
            'head' => true
        ]);
    }

    public function getRelaysForArgument($avm, $argument)
    {
        if (strpos($argument, '.') !== false) {
            list($pool, $pscf) = explode('.', $argument);
            $attribute = $avm->attributes->$pool;
            $idAttr = $attribute->id;
            $relays = $this->relays[$idAttr][$pscf];
        } else {
            $pool = $argument;
            $attribute = $avm->attributes->$pool;
            $idAttr = $attribute->id;
            $relays = $this->relays[$idAttr];
        }
        return $relays;
    }

    public function createConstraint($constraint)
    {
        $typeNodeConstraint = $this->typeNetwork->getOrCreateNode($constraint->idConstraint);
        $argument1Relays = $this->relays[$constraint->idPool1];
        $argument2Relays = $this->relays[$constraint->idPool2];
        if (count($argument1Relays)) {
            foreach ($argument1Relays as $argument1Relay) {
                foreach ($argument2Relays as $argument2Relay) {
                    $idConstraint = $this->getIdFor($argument1Relay->id . $argument2Relay->id . $typeNodeConstraint->type);
                    $nodeConstraint = $this->typeNetwork->getOrCreateNode($idConstraint, (object)[
                        'logic' => 'C',
                        'name' => $typeNodeConstraint->name,
                        'type' => $typeNodeConstraint->type,
                        'class' => $typeNodeConstraint->class,
                        'region' => $argument1Relay->region
                    ]);
                    $this->typeNetwork->createLink($argument1Relay, $nodeConstraint, (object)[
                        'optional' => false,
                        'label' => 'rel_argument1'
                    ]);
                    $this->typeNetwork->createLink($argument2Relay, $nodeConstraint, (object)[
                        'optional' => false,
                        'label' => 'rel_argument2'
                    ]);
                }
            }
        }
    }


    public function getIdFor($value)
    {
        $id = md5($value);
        return $id;
    }
}

