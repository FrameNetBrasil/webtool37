<?php

namespace C5\Service;

use Symfony\Component\Yaml\Yaml;
use C5\ORM\Service\GraphService;
use C5\ORM\Service\C5Service;
use C5\Network\FullNetwork;

class ResourceFull extends Resource
{
    public $idLanguage;
    public $graphService;
    public $dataService;
    public $fullNetwork;
    public $nodeType;

    public function __construct()
    {
        parent::__construct();
        $this->idLanguage = 1;
        $this->nodeType = [
            'CX' => 'Cxn',
            'FR' => 'Frame',
            'CE' => 'CE',
            'CP' => 'Concept',
            'CN' => 'Constraint',
            'UR' => 'UDRelation'
        ];
    }

    public function setGraphService(GraphService $graphService)
    {
        $this->graphService = $graphService;
    }

    public function setDataService(C5Service $dataService)
    {
        $this->dataService = $dataService;
    }

    public function setFullNetwork(FullNetwork $fullNetwork) {
        $this->fullNetwork = $fullNetwork;
    }

    public function clean()
    {
        $this->graphService->execute("delete from C5Link");
        $this->graphService->execute("delete from C5Node");
    }

    public function loadFull($idConcepts = [])
    {
        try {
            $nodes = $this->dataService->getAllNodes();
            $i = 0;
            foreach ($nodes as $node) {
                $type = $node['type'];
                $idEntity = $node['idEntity'];
                $ok = true;
                if ($type == 'CP') {
                    if (count($idConcepts) > 0) {
                        if (!in_array($idEntity, $idConcepts)) {
                            $ok = false;
                        }
                    }
                }
                if ($ok) {
                    $id = 'node_' . $node['idEntity'];
                    $this->graphService->createNode([
                        'id' => $id,
                        'name' => $node['name'],
                        'idEntity' => $node['idEntity'],
                        'type' => $this->nodeType[$node['type']],
                        'class' => 'node',
                        'category' => 'node',
                        'region' => 'node'
                    ]);
                    if ((++$i % 200) == 0) {
                        $this->dump('Nodes = ' . $i);
                    }
                }
            }
            $relations = $this->dataService->getAllLinks();
            $i = 0;
            foreach ($relations as $relation) {
                $idSource = 'node_' . $relation['idSource'];
                $idTarget = 'node_' . $relation['idTarget'];
                $type = $relation['relationType'];
                //$idRelation = $idSource . '_' . $type . '_' . $idTarget;
                // links argument
                $this->graphService->createLink($idSource, $idTarget, $type);
                if ((++$i % 200) == 0) {
                    $this->dump('Relations = ' . $i);
                }
            }
        } catch (\Exception $e) {
            $this->dump($e->getMessage());
            die;
        }
    }

    public function loadFullNetwork()
    {
        $this->processed = [];
        $types = ['cxn','ce','frame','udrelation','concept','constraint'];
        foreach($types as $type) {
            $data = $this->graphService->listNodesByType($type);
            foreach ($data as $n) {
                $node = $this->fullNetwork->getOrCreateNode($n['id']);
                $this->loadNode($node);
            }
        }
        $links = $this->graphService->listLinks();
        foreach($links as $link) {
            $params = (object)$link;
            $this->fullNetwork->createLinkById($link['idSource'], $link['idTarget'], $params);
        }

    }

    private function loadNode($node)
    {
        if ($this->processed[$node->id]) {
            return;
        }
        $relays = [];
        $allElements = [];
        // elements
        $elements = $this->graphService->getPoolsOf($nodeUD->id);
        foreach ($elements as $element) {
            $nodeElement = $this->fullNetwork->getOrCreateNode($element->id, $element);
            $this->fullNetwork->createLink($nodeElement, $nodeUD, (object)[
                'optional' => $element->optional,
                'label' => 'rel_elementof',
                'head' => $element->head
            ]);
            $allElements[] = $nodeElement;
        }
        // values of elements - só link se existir na rede
        foreach ($allElements as $nodeElement) {
            $values = $this->graphService->getValuesOf($nodeElement->id);
            foreach ($values as $value) {
                $nodeValue = $this->fullNetwork->getNode($value['value']->id);
                if ($nodeValue) {
                    $nodeRelay = $this->fullNetwork->getOrCreateNode($value['r']->id, $value['r']);
                    $this->fullNetwork->createLink($nodeValue, $nodeRelay, (object)[
                        'optional' => $nodeElement->optional,
                        'label' => 'rel_value',
                        'head' => $nodeElement->head
                    ]);
                    $this->fullNetwork->createLink($nodeRelay, $nodeElement, (object)[
                        'optional' => $nodeElement->optional,
                        'label' => 'rel_value',
                        'head' => $nodeElement->head
                    ]);
                    $relays[] = $nodeRelay->id;
                }
            }
        }
        // verifica as constraints entre os relays que foram criados
        $constraints = $this->graphService->getConstraints($relays);
        foreach ($constraints as $constraint) {
            $typeNodeConstraint = $this->graphService->getNodeById($constraint['c']->type);
            $nodeConstraint = $this->fullNetwork->getOrCreateNode($constraint['c']->id, (object)[
                'logic' => 'C',
                'name' => $constraint['c']->name,
                'type' => $typeNodeConstraint->type,
                'class' => $typeNodeConstraint->class,
                'region' => $constraint['c']->region
            ]);
            $nodeR1 = $this->fullNetwork->getNode($constraint['r1']->id);
            $nodeR2 = $this->fullNetwork->getNode($constraint['r2']->id);
            $this->fullNetwork->createLink($nodeR1, $nodeConstraint, (object)[
                'optional' => false,
                'label' => 'rel_argument1',
                'head' => false
            ]);
            $this->fullNetwork->createLink($nodeR2, $nodeConstraint, (object)[
                'optional' => true,
                'label' => 'rel_argument2',
                'head' => false
            ]);

        }
        $this->processed[$nodeUD->id] = true;
    }

    public function countNodes()
    {
        return $this->graphService->countNodes() . ' nodes; ' . $this->graphService->countLinks() . ' links';
    }

}

