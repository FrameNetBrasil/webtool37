<?php

namespace C5\Network;

use Mesh\Element\Node\TypeNode;
use C5\ORM\Service\GraphService;

class FullNetwork extends \Mesh\Element\Network\TypeNetwork
{
    public $graphService;

    public function setGraphService(GraphService $graphService)
    {
        $this->graphService = $graphService;
    }

    public function createNode($id, $params)
    {
        $node = new TypeNode($id);
        $node->setParams($params);
        $this->addNode($node);
        return $node;
    }

    public function getOrCreateNode($id, $params = NULL)
    {
        $node = $this->getNode($id);
        if (is_null($node)) {
            if ($params) {
                $node = $this->createNode($id, $params);
            } else {
                $params = $this->graphService->getNodeById($id);
                $node = $this->createNode($id, $params);
            }
        }
        return $node;
    }

    public function clearAll()
    {
        foreach ($this->nodes as $node) {
            $id = $node->getId();
            unset($this->nodesByName[$node->getName()]);
            unset($this->nodes[$id]);
            foreach ($this->links as $idSource => $targets) {
                foreach ($targets as $idTarget => $link) {
                    if ($idTarget == $id) {
                        unset($this->links[$idSource][$idTarget]);
                    }
                }
            }
            unset($this->links[$id]);
            unset($this->nodesInput[$id]);
            unset($this->nodesOutput[$id]);
        }
    }

    public function getStructure()
    {
        $structure = (object)[
            'nodes' => [],
            'links' => [],
            'groups' => []
        ];
        $slots = [];
        $i = 0;
        $validNodes = [];
        $cola = [];
        $regions = [];
        $nodes = [];
        ksort($this->nodes);
        foreach ($this->nodes as $node) {
            //$this->dump('>>>>>>>>>>>>  ' . $node->id . ' - ' . $node->label . ' - ' . $node->type . $node->idHead);
            $validNodes[$node->getId()] = 1;
            $cola[$node->getId()] = $node->getId();
            $nodes[$node->getId()] = $node;
            $i++;
        }
        foreach ($this->nodes as $node) {
            $idSource = $node->getId();
            if (count($this->links[$idSource])) {
                foreach ($this->links[$idSource] as $idTarget => $link) {
                    if (isset($cola[$idSource]) && isset($cola[$idTarget])) {
                        $iSourceNode = $cola[$idSource];
                        $iTargetNode = $cola[$idTarget];
                        $sourceNode = $nodes[$idSource];
                        $targetNode = $nodes[$idTarget];
                        if (!isset($structure->nodes[$iSourceNode])) {
                            $structure->nodes[$iSourceNode] = [
                                'index' => $cola[$idSource],
                                'id' => $sourceNode->getId(),
                                'name' => $sourceNode->getName(),
                                'position' => $sourceNode->index,
                                'activation' => 0.0,
                                'type' => $sourceNode->getType(),
                                'class' => $sourceNode->getClass(),
                                'status' => $sourceNode->status,
                                'phase' => $sourceNode->phase,
                                'region' => $sourceNode->region,
                                'logic' => $sourceNode->logic,
                                'group' => $sourceNode->group
                            ];
                            $regions[$sourceNode->region] = $sourceNode->region;

                        }
                        if (!isset($structure->nodes[$iTargetNode])) {
                            $structure->nodes[$iTargetNode] = [
                                'index' => $cola[$idTarget],
                                'id' => $targetNode->getId(),
                                'name' => $targetNode->getName(),
                                'position' => $targetNode->index,
                                'activation' => 0.0,
                                'type' => $targetNode->getType(),
                                'class' => $targetNode->getClass(),
                                'status' => $targetNode->status,
                                'phase' => $targetNode->phase,
                                'region' => $targetNode->region,
                                'logic' => $targetNode->logic,
                                'group' => $targetNode->group
                            ];
                            $regions[$targetNode->region] = $targetNode->region;

                        }
                        $structure->links[] = [
                            'source' => $iSourceNode,
                            'target' => $iTargetNode,
                            'label' => $link->label ?: 'rel_common',
                            'status' => 'active',
                            'optional' => $link->optional,
                            'head' => $link->head
                        ];
                    }

                }
            }
        }
        $structure->regions = $regions;
        return $structure;
    }

}

