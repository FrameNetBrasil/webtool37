<?php

namespace C5\Network;

class ConceptNetwork extends \Mesh\Element\Network\TokenNetwork
{
    public $typeNodes;
    public $nextNodes;
    public $tokens;
    public $tokenLinks;

    public function __construct() {
        parent::__construct();
        $this->typeNodes = [];
        $this->nextNodes = [];
        $this->tokens = [];
        $this->tokenLinks = [];
    }

    public function setTypeNetwork(FullNetwork $typeNetwork)
    {
        $this->typeNetwork = $typeNetwork;
    }

    public function build($idCxn)
    {
        //$baseNode = $this->typeNetwork->getNodeByName($cxn);
        $idCxn = 'node_' . $idCxn;
        mdump($idCxn);
        $baseNode = $this->typeNetwork->getNode($idCxn);
        $idBaseNode = $baseNode->getId();

        $next = [$idBaseNode];
        while (count($next) > 0) {
            $this->dump('= outer =========');
            $nextNodes = [];
            foreach ($next as $idTypeNode) {
                $typeNode = $this->typeNetwork->getNode($idTypeNode);
                $this->dump('- begin ------- ' . $typeNode->name);
                if(!isset($processed[$idTypeNode])) {
                    $baseToken = $this->createTokenById($idTypeNode);
                    if ($idTypeNode == $idBaseNode) {
                        $baseToken->isQuery = true;
                    }
                    $idNextNodes = $this->typeNetwork->getIdNodesOutput($idTypeNode);
                    if (count($idNextNodes)) {
                        foreach ($idNextNodes as $idNextNode) {
                            $nextNode = $this->typeNetwork->getNode($idNextNode);
                            $link = $this->typeNetwork->getLink($typeNode, $nextNode);
                            $ok = true;
                            if (($typeNode->type == 'CE') && ($nextNode->type == 'Cxn')) {
                                $ok = $baseToken->isQuery || ($link->getLabel() == 'rel_elementof');
                            }
                            if (($typeNode->type == 'Cxn') && ($nextNode->type == 'Constraint')) {
                                $ok = $baseToken->isQuery;
                            }
                            if (($typeNode->type == 'Concept') && ($nextNode->type == 'Concept')) {
                                $ok = false;
                            }
                            $this->dump("## out " . $typeNode->name .':'. $nextNode->name .'  '.  $link->getLabel(). '  '.  $typeNode->type . ':' . $nextNode->type . '  ok = ' . ($ok ? 'true' : 'false'));
                            if ($ok) {
                                if (!isset($this->tokens[$idNextNode])) {
                                    $nextToken = $this->createTokenById($idNextNode);
                                } else {
                                    $nextToken = $this->tokens[$idNextNode];
                                }
                                $this->createTokenLink($baseToken, $nextToken);
                                $nextNodes[$idNextNode] = $idNextNode;
                            }
                        }
                    }
                    $idNextNodes = $this->typeNetwork->getIdNodesInput($idTypeNode);
                    if (count($idNextNodes)) {
                        foreach ($idNextNodes as $idNextNode) {
                            $nextNode = $this->typeNetwork->getNode($idNextNode);
                            $link = $this->typeNetwork->getLink($nextNode, $typeNode);
                            $ok = true;
                            if (($typeNode->type == 'Cxn') && ($nextNode->type == 'CE')) {
                                //$ok = ($idTypeNode == $idBaseNode) || ($link->getLabel() == 'rel_constraint_cxn');
                                $ok = $baseToken->isQuery;
                            }
                            if (($typeNode->type == 'Cxn') && ($nextNode->type == 'Constraint')) {
                                $ok = false;
                            }
                            if (($typeNode->type == 'Constraint') && ($nextNode->type == 'CE')) {
                                $ok = false;
                            }
                            if (($typeNode->type == 'Concept') && ($nextNode->type == 'Concept')) {
                                $ok = true;
                            }
                            $this->dump("## in " . $typeNode->name .':'. $nextNode->name . '  '.  $link->getLabel(). '  ' . $typeNode->type . ':' . $nextNode->type . '  ok = ' . ($ok ? 'true' : 'false'));
                            if ($ok) {
                                if (!isset($this->tokens[$idNextNode])) {
                                    $nextToken = $this->createTokenById($idNextNode);
                                } else {
                                    $nextToken = $this->tokens[$idNextNode];
                                }
                                $this->createTokenLink($baseToken, $nextToken);
                                $nextNodes[$idNextNode] = $idNextNode;
                            }
                        }
                    }
                    $this->typeNodes[$idTypeNode] = $idTypeNode;
                    $processed[$idTypeNode] = $idTypeNode;
                }
                $this->dump('- end -------');
            }
            $next = $nextNodes;
            $this->dump('= end outer ========= ' . count($next) . ' next');
        }
    }

    public function createToken($typeNode)
    {
        $id = $typeNode->getId();
        if (isset($this->tokens[$id])) {
            return $this->tokens[$id];
        }
        $tokenNode = $this->createNodeToken($typeNode);
        $this->tokens[$id] = $tokenNode;
        return $tokenNode;
    }

    public function createTokenById($idTypeNode)
    {
        return $this->createToken($this->typeNetwork->getNode($idTypeNode));
    }

    public function createTokenLink($baseToken, $nextToken) {
        $idBaseToken = $baseToken->id;
        $idNextToken = $nextToken->id;
        if ((!isset($this->tokenLinks[$idBaseToken][$idNextToken])) && (!isset($this->tokenLinks[$idNextToken][$idBaseToken]))) {
            $baseToken->createLinkTo($nextToken);
            $this->tokenLinks[$idBaseToken][$idNextToken] = 1;
            if (($baseToken->type == 'Cxn') && ($nextToken->type == 'CE')) {
                $nextToken->isQuery = $baseToken->isQuery;
            }

        }
    }

    /*
     * Activation
     */

    public function activate($idCxn)
    {
        $this->currentLayer = 0;
        $this->currentPhase = 'feature';
        //$baseNode = $this->typeNetwork->getNodeByName($cxn);
        $idCxn = 'node_' . $idCxn;
        $baseNode = $this->typeNetwork->getNode($idCxn);
        $idBaseNode = $baseNode->getId();
        $baseToken = $this->tokens[$idBaseNode];
        $baseToken->status = 'active';
        $baseToken->a = 1;
        $this->nextNodes = $baseToken->fire();
        $end = (count($this->nextNodes) == 0);
        return $end;
    }

    public function activateNext()
    {
        $this->dump('*************************************************************');
        $this->dump('***** activateNext');
        $this->dump('*************************************************************');
        $next = [];
        foreach($this->nextNodes as $nextNode) {
            $nextNodes = $nextNode->process();
            if(count($nextNodes)) {
                foreach($nextNodes as $nextNode) {
                    $next[] = $nextNode;
                }
            }
        }
        $this->nextNodes = $next;
        $end = (count($this->nextNodes) == 0);
        $this->dump('*************************************************************');
        $this->dump('***** end activateNext - more? ' . ($end ? 'no' : 'yes'));
        $this->dump('*************************************************************');
        foreach($this->nextNodes as $nextNode) {
            $this->dump('-> ' . $nextNode->name);
        }
        return $end;
    }

    /*
     *
     */

    public function getStructure()
    {
        $structure = (object)[
            'nodes' => [],
            'links' => [],
            'groups' => [],
        ];
        $i = 0;
        $validNodes = [];
        $cola = [];
        $regions = [];
        ksort($this->nodes);
        foreach ($this->nodes as $node) {
            $type = $node->getType();
            $sitesOut = $this->siteList->getOutputSites($node->id);
            if ((count($sitesOut) > 0) || ($type == 'Cxn')) {
                mdump($node->getName() . ' a = ' . $node->a);
                $validNodes[$node->getId()] = 1;
                $cola[$node->getId()] = $node->getId();
                $structure->nodes[$i] = [
                    'index' => $i,
                    'id' => $node->getId(),
                    'name' => $node->getName(),
                    'position' => $node->index,
                    'activation' => $node->a, //round($node->getA(), 5),
                    'type' => $node->getType(),
                    'class' => $node->getClass(),
                    'status' => $node->status,
                    'phase' => $node->phase,
                    'region' => $node->region,
                    'logic' => $node->logic,
                    'idHead' => $node->idHead,
                    'wordIndex' => $node->wordIndex,
                    'h' => $node->h,
                    'd' => $node->d,
                    'w' => $node->w,
                    'strSlots' => substr($node->getSlotsStr(), 1),
                    'slots' => $node->getSlots(),
                    'isQuery' => $node->isQuery,
                    'layer' => $node->layer,
                    'group' => $node->group
                ];
                $this->statusMemory[$node->getId()] = $node->status;
                $regions[$node->region] = $node->region;
                $i++;
            }
        }
        foreach ($this->nodes as $node) {
            foreach ($this->siteList->getOutputSites($node->id) as $site) {
                $idSource = $node->getId();
                $idTarget = $site->idTargetToken;
                if (isset($cola[$idSource]) && isset($cola[$idTarget])) {
                    $sourceNode = $cola[$idSource];
                    $targetNode = $cola[$idTarget];
                    $status = ($site->active() ? 'active' : 'inactive');
                    $structure->links[] = [
                        'source' => $sourceNode,
                        'target' => $targetNode,
                        'label' => $site->label() ?: 'rel_common',
                        'status' => $status,
                        'optional' => $site->optional(),
                        'head' => $site->head()
                    ];
                    $this->statusMemory[md5($idSource . '-' . $idTarget)] = $status;
                }

            }
        }
        $structure->regions = $regions;
        return $structure;
    }

    public function getCxnNodes()
    {
        $cxnNodes = [];
        foreach ($this->nodes as $node) {
            $type = $node->getType();
            if ($type == 'Cxn') {
                $idEntity = str_replace('_1','', str_replace('node_', '',$node->getId()));
                if ($node->isQuery) {
                    $a = 1;
                } else {
                    $sta = -1 * $node->a;
                    $a = (1 - exp(5 * $sta)) / (1 + exp(2 * $sta));
                }
                $cxnNodes[] = [
                    'idEntity' => $idEntity,
                    'a' => $a
                ];
            }
        }
        return $cxnNodes;
    }

}

