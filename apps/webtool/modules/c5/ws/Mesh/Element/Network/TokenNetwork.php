<?php

namespace Mesh\Element\Network;

use Mesh\Element\Structure\ {
    SiteList, Link
};
use Mesh\Element\Node\ {
    NodeRelay, NodeConstraint
};
use Mesh\Node\Feature\ {
    NodeWord, NodePOS, NodeUD, NodeLexeme, NodeLU, NodeCxn, NodeFrame, NodeRelation, NodeDepRel, NodeUDRelation, NodeInhibitory, NodeRoot
};
use Mesh\Node\Pool\ {
    NodeFE, NodeCE, NodeRE, NodeXE
};
use Mesh\Node\Constraint\ {
    NodeConstraintAnd,
    NodeConstraintAfter,
    NodeConstraintBefore,
    NodeConstraintDifferent,
    NodeConstraintDominance,
    NodeConstraintDomino,
    NodeConstraintHead,
    NodeConstraintFollows,
    NodeConstraintMeets,
    NodeConstraintSame,
    NodeConstraintHasWord,
    NodeConstraintXOR,
    NodeConstraintNear
};
use Mesh\Infra\ {
    GraphViz
};

class TokenNetwork extends Network
{
    public $typeNetwork;
    public $firstNode;
    public $siteList;
    public $wordOrder; // posição da palavra na sentença
    public $activations; // número de tokens que já foram instanciados para um dado type
    public $projections; // count for projections of a featureNode
    public $tokensByType; // listas dos tokens instanciados pelo idNodeType
    public $tokensByClass; // listas dos tokens instanciados pela class
    public $statusMemory;
    public $className;
    public $rootNodes;
    public $activation; // incrementado cada vez que um nó é processado

    public function __construct()
    {
        parent::__construct();
        $this->siteList = new SiteList();
        $this->activation = 0;
        $this->activations = [];
        $this->projections = [];
        $this->tokensByType = [];
        $this->tokensByClass = [];
        $this->rootNodes = [];
        $this->wordOrder = 1;
        /*
        $this->className = [
            'word' => NodeWord::class,
            'lexeme' => NodeLexeme::class,
            'lemma' => NodeLemma::class,
            'lu' => NodeLU::class,
            'pos' => NodePOS::class,
            'ud' => NodeUD::class,
            'cxn' => NodeCxn::class,
            'frame' => NodeFrame::class,
            'relation' => NodeRelation::class,
            'udrelation' => NodeUDRelation::class,
            'deprel' => NodeDepRel::class,
            'inhibitory' => NodeInhibitory::class,
            'root' => NodeRoot::class,
            'ce' => NodeCE::class,
            'fe' => NodeFE::class,
            're' => NodeRE::class,
            'xe' => NodeXE::class,
            'le' => NodeLE::class,
            'relay' => NodeRelay::class,
            'constraint' => NodeConstraint::class,
            'constraintand' => NodeConstraintAnd::class,
            'constraintnear' => NodeConstraintNear::class,
            'constraintbefore' => NodeConstraintBefore::class,
            'constraintafter' => NodeConstraintAfter::class,
            'constraintdifferent' => NodeConstraintDifferent::class,
            'constraintdominance' => NodeConstraintDominance::class,
            'constraintdomino' => NodeConstraintDomino::class,
            'constrainthead' => NodeConstraintHead::class,
            'constraintsame' => NodeConstraintSame::class,
            'constraintxor' => NodeConstraintXOR::class,
            'constraintfollows' => NodeConstraintFollows::class,
            'constraintmeets' => NodeConstraintMeets::class,
            'constrainthasword' => NodeConstraintHasWord::class,
        ];
        */
    }

    public function setTypeNetwork(TypeNetwork $typeNetwork)
    {
        $this->typeNetwork = $typeNetwork;
    }

    public function clearAll()
    {
        foreach ($this->nodes as $node) {
            $id = $node->getId();
            unset($this->nodes[$id]);
        }
        $this->nodes = [];
        $this->nodesByName = [];
        $this->nodesByFullName = [];
        $this->nodesByType = [];
        $this->nodesByClass = [];
        $this->nodesByRegion = [];
        $this->activations = [];
        $this->tokensByType = [];
        $this->tokensByClass = [];
        $this->statusMemory = [];
        $this->rootNodes = [];
        $this->siteList = new SiteList();
    }

    public function createNode($id, $params = [])
    {
        //$this->manager->dump($params);
        //$className = $this->className[$params['type']];
        $className = 'Node'. $params['type'];
        //$this->manager->dump('creating token node classname = ' . $className);
        //$node = new $className($id);
        //print_r('createNode: ' . $className . ' name = ' . $params['name'] ."\n");
        $node = $this->container->make($className);
        $node->setId($id);
        $node->setTokenNetwork($this);
        $node->setParams($params);
        $this->addNode($node);
        $node->manager = $this->manager;
        if ($node instanceof NodeRoot) {
            $this->rootNodes[] = $node;
        }
        return $node;
    }

    public function createNewToken($params) {
        $params->activation = 1;
        $id = md5(uniqid($params->name));
        $tokenNode = $this->getOrCreateNode($id, $params);
        $tokenNode->debug = $this->debug;
        $tokenNode->typeNode = $tokenNode;
        $this->siteList->initialize($id);
        if ($params->class != '') {
            $this->pushTokenByClass($params->class, $tokenNode);
        }
        return $tokenNode;
    }

    public function createNodeToken($typeNode, $tokenForProjection = null)
    {
        if (is_null($tokenForProjection)) {
            $activation = ++$this->activations[$typeNode->id];
            $id = $typeNode->id . '_' . $activation;
        } else {
            $activation = ++$this->projections[$typeNode->id];
            $id = $tokenForProjection->id . '_n_' . $activation;
        }
        $name = $typeNode->getName() . '_' . $activation;
        $params = [
            'type' => $typeNode->type,
            'name' => $name,
            //'activation' => $activation,
            'status' => 'inactive',
            'logic' => $typeNode->logic,
            'class' => $typeNode->class,
            'category' => $typeNode->category,
            'h' => $typeNode->wordIndex,//$typeNode->h,
            'd' => $typeNode->wordIndex,//$typeNode->d,
            'idHead' => $typeNode->idHead,
            'wordIndex' => $typeNode->wordIndex
        ];
        $tokenNode = $this->getOrCreateNode($id, $params);
        $tokenNode->typeNode = $typeNode;
        $tokenNode->region = ($typeNode->region ?: $this->region);
        $tokenNode->debug = $this->debug;
        $this->siteList->initialize($id);
        $this->pushTokenByType($typeNode->id, $tokenNode);
        if ($typeNode->class != '') {
            $this->pushTokenByClass($typeNode->class, $tokenNode);
        }
        // inicializa o slot no caso de words
        if (($typeNode->type == 'word') && ($activation == 1)) {
            $tokenNode->setLayer(0);
            //$tokenNode->wordIndex = $typeNode->wordIndex;
            $tokenNode->getSlots()->set($typeNode->wordIndex);
            //++$this->wordOrder;
        }
        return $tokenNode;
    }

    /*
    public function createNodeTokenProjection($typeNode)
    {
        $tokenNode = $this->createNodeToken($typeNode);
        $tokenNode->region = $this->region;
        $tokenNode->logic = 'N';
        return $tokenNode;
    }

    public function createNodeTokenById($idBaseNode)
    {
        return $this->createNodeToken($this->typeNetwork->getNode($idBaseNode));
    }
*/
    public function getLinkFor($sourceNodeToken, $targetNodeToken)
    {
        $link = $this->typeNetwork->getLink($sourceNodeToken->typeNode, $targetNodeToken->typeNode);
        if (!($link instanceof Link)) {
            $link = $this->typeNetwork->getLinkByClass($sourceNodeToken->typeNode, $targetNodeToken->typeNode);
        }
        if (!($link instanceof Link)) {
            $link = $this->typeNetwork->createLinkById($sourceNodeToken->id, $targetNodeToken->id, [
                'label' => 'rel_value',
                'optional' => $sourceNodeToken->optional,
                'head' => $sourceNodeToken->head
            ]);
        }
        return $link;
    }


    public function createSite($sourceNodeToken, $targetNodeToken, $link = null)
    {
        if ($link == null) {
            $link = $this->getLinkFor($sourceNodeToken, $targetNodeToken);
        }
        $this->siteList->createSite($sourceNodeToken, $targetNodeToken, $link);
    }

    /*
    public function createSites($sourceNodeToken, $targetNodeToken, $link = null)
    {
        if ($link == null) {
            $link = $this->getLinkFor($sourceNodeToken, $targetNodeToken);
        }
        $this->siteList->createSites($sourceNodeToken, $targetNodeToken, $link);
    }
    */

    public function pushTokenByClass($class, $nodeToken)
    {
        $this->tokensByClass[$class][] = $nodeToken;
    }

    public function getTokensByClass($class)
    {
        return (isset($this->tokensByClass[$class]) ? $this->tokensByClass[$class] : []);
    }

    public function pushTokenByType($idTypeNode, $nodeToken)
    {
        $this->tokensByType[$idTypeNode][] = $nodeToken;
    }

    public function getTokensByType($idNodeType)
    {
        return (isset($this->tokensByType[$idNodeType]) ? $this->tokensByType[$idNodeType] : []);
    }

    public function getAvailable($idNodeType, $ident = '')
    {
        $available = [];
        $list = $this->getTokensByType($idNodeType);
        foreach ($list as $candidateToken) {
            if ($candidateToken->logic != 'N') { // não é um projection
                $available[] = $candidateToken;
            }
        }
        return $available;
    }

    public function getTokensAvailableByTypeOrClass($idNodeType, $ident = '')
    {
        $available = $this->getAvailable($idNodeType);
        if (count($available) == 0) {
            $list = $this->getTokensByClass($idNodeType);
            foreach ($list as $candidateToken) {
                if ($candidateToken->logic != 'N') { // não é um projection
                    $available[] = $candidateToken;
                }
            }
        }
        return $available;
    }

    public function getStructure()
    {
        $structure = (object)[
            'nodes' => [],
            'links' => [],
            'groups' => [],
            'words' => $this->manager->getWords()
        ];
        $fnNodes = ['word', 'lexeme', 'le', 'lemma', 'lu', 'fe', 'frame', 'relay', 'constraint', 'relation'];
        $slots = [];
        $i = 0;
        $validNodes = [];
        $cola = [];
        $regions = [];
        ksort($this->nodes);
        foreach ($this->nodes as $node) {
            if ($this->onlyFN) {
                $type = $node->getType();
                if (!in_array($type, $fnNodes)) {
                    continue;
                }
            }

            //$this->dump('>>>>>>>>>>>>  ' . $node->id . ' - ' . $node->label . ' - ' . $node->type . ' - ' . $node->status . ' -  ' . $node->energy . ' -  ' . $node->a . ' -  ' . $node->idHead);
            $validNodes[$node->getId()] = 1;
            //$cola[$node->getId()] = $i;
            $cola[$node->getId()] = $node->getId();
            if ($node->layer == 2) {
                print_r();
            }
            $structure->nodes[$i] = [
                'index' => $i,
                'id' => $node->getId(),
                'name' => $node->getName(),
                'position' => $node->index,
                'activation' => $node->activation, //round($node->getA(), 5),
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
                'layer' => $node->layer,
                'group' => $node->group
            ];
            $this->statusMemory[$node->getId()] = $node->status;
            $regions[$node->region] = $node->region;
            $i++;
        }
        //$layers = [];
        //foreach ($regions as $region) {
        //    $layers[$region] = $this->manager->regionNetwork->getLayerByRegion($region);
        //}
        foreach ($this->nodes as $node) {
            foreach ($this->siteList->getOutputSites($node->id) as $site) {
                $idSource = $node->getId();
                $idTarget = $site->idTargetToken;
                if (isset($cola[$idSource]) && isset($cola[$idTarget])) {
                    $sourceNode = $cola[$idSource];
                    $targetNode = $cola[$idTarget];

                    //if ($site->isCloned()) {
                    //    continue;
                    //}
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
        $structure->layers = $layers;
        return $structure;
    }

    public function getUpdatedStructure()
    {
        $structure = (object)[
            'nodes' => [],
            'links' => []
        ];
        $cola = [];
        $i = 0;
        foreach ($this->nodes as $node) {
            if ($node->status != $this->statusMemory[$node->getId()]) {
                $cola[$node->getId()] = $node->getId();
                $structure->nodes[$i] = [
                    'index' => $i,
                    'id' => $node->getId(),
                    'name' => $node->getName(),
                    'position' => $node->index,
                    'activation' => round($node->getA(), 5),
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
                    'slots' => $node->getSlotsStr(),
                    'layer' => $node->layer,
                    'group' => $node->group
                ];
                $this->statusMemory[$node->getId()] = $node->status;
                $i++;
            }
        }
        foreach ($this->nodes as $node) {
            foreach ($this->siteList->getOutputSites($node->id) as $site) {
                $idSource = $node->getId();
                $idTarget = $site->idTargetToken;
                //if (isset($cola[$idSource]) && isset($cola[$idTarget])) {
                $status = ($site->active() ? 'active' : 'inactive');
                if ($status != $this->statusMemory[md5($idSource . '-' . $idTarget)]) {
                    //$sourceNode = $cola[$idSource];
                    //$targetNode = $cola[$idTarget];
                    $structure->links[] = [
                        'source' => $idSource,
                        'target' => $idTarget,
                        'label' => $site->label() ?: 'rel_common',
                        'status' => $status,
                        'optional' => $site->optional(),
                        'head' => $site->head()
                    ];
                    $this->statusMemory[md5($idSource . '-' . $idTarget)] = $status;
                }
                //}

            }
        }
        return $structure;
    }

    public function getUpdatedGraph()
    {
        $updatedStructure = $this->getUpdatedStructure();
        $graphviz = new GraphViz($updatedStructure);
        $graphObj = $graphviz->createGraph();
        $graph = [
            'nodes' => $graphObj->graph['nodes'],
            'edges' => $graphObj->graph['edgesFrom']
        ];
        return json_encode($graph);
    }

}

