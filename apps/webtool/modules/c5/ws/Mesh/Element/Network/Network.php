<?php

namespace Mesh\Element\Network;

use Mesh\Infra\Base;
use Mesh\Infra\GraphViz;
use Mesh\Element\Node\Node;
/**
 * Class Network
 * Infraestrutura de nós de rede, herdada por todas as *Network
 */
class Network extends Base
{
    public $nodes;
    public $nodesByName;
    public $nodesByFullName;
    public $nodesByType;
    public $nodesByClass;
    public $nodesByRegion;
    public $graphviz;

    public function __construct()
    {
        parent::__construct();
        $this->nodes = [];
        $this->nodesByName = [];
        $this->nodesByFullName = [];
        $this->nodesByType = [];
        $this->nodesByClass = [];
        $this->nodesByRegion = [];
    }

    public function setGraphViz($graphviz) {
        $this->graphviz = $graphviz;
    }

    public function addNode(Node $node)
    {
        $idNode = $node->getId();
        $this->nodes[$idNode] = $node;
        $this->nodesByName[$node->getName()] = $idNode;
        $this->nodesByFullName[$node->getFullName()] = $idNode;
        $this->nodesByType[$node->getType()][] = $idNode;
        if ($node->getClass() != '') {
            $this->nodesByClass[$node->getClass()][] = $idNode;
        }
        if ($node->region != '') {
            $this->nodesByRegion[$node->region][] = $idNode;
        }
    }

    public function removeNode(Node $node)
    {
        unset($this->nodes[$node->getId()]);
        unset($this->nodesByName[$node->getName()]);
        unset($this->nodesByFullName[$node->getFullName()]);
        unset($this->nodesByType[$node->getType()]);
        unset($this->nodesByClass[$node->getClass()]);
        unset($this->nodesByRegion[$node->region]);
    }

    public function getNode($id)
    {
        return isset($this->nodes[$id]) ? $this->nodes[$id] : null;
    }

    public function getNodeByName($name)
    {
        return $this->getNode($this->nodesByName[$name]) ?: null;
    }

    public function getNodeByFullName($fullName)
    {
        return $this->getNode($this->nodesByFullName[$fullName]) ?: null;
    }

    public function getIdNodesByType($type)
    {
        return is_array($this->nodesByType[$type]) ? $this->nodesByType[$type] : [];
    }

    public function getIdNodesByClass($class)
    {
        return isset($this->nodesByClass[$class]) ? $this->nodesByClass[$class] : [];
    }

    public function getIdNodesByRegion($region)
    {
        return is_array($this->nodesByRegion[$region]) ? $this->nodesByRegion[$region] : [];
    }

    public function createNode($id, $params)
    {
    }

    public function getOrCreateNode($id, $params)
    {
        $node = $this->getNode($id);
        if (is_null($node)) {
            $node = $this->createNode($id, $params);
        }
        return $node;
    }

    public function getStructure()
    {
        $structure = (object)[
            'nodes' => [],
            'links' => [],
            'regions' => [],
            'groups' => []
        ];
        return $structure;
    }

    public function getStructureGraphViz()
    {
        $structure = $this->getStructure();
        $this->graphviz->setStructure($structure);
        $dot = $this->graphviz->generateDot();
        return $dot;
    }


}

