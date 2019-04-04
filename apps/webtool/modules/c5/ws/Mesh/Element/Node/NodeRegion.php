<?php

namespace Mesh\Element\Node;

class NodeRegion extends Node
{
    public $regionNetwork;
    public $tokenNetwork;
    public $typeNetwork;
    public $layer;
    public $region;
    public $tokens;
    public $tokensByType;
    public $constraintType;
    public $constraintToken;

    public $relaysByProjection;
    public $projectionsByFeature;

    public function __construct($regionNetwork, $baseRegion)
    {
        $this->regionNetwork = $regionNetwork;
        $this->tokenNetwork = $regionNetwork->tokenNetwork;
        $this->region = $regionNetwork->region;
        $this->layer = $regionNetwork->layer;
        $this->tokens = [];
        $id = md5($this->region . $this->layer);
        parent::__construct($id);
        $this->name = $this->region;
        $this->type = 'region';
        $this->tokensByType = [];
        $this->tokensByType['relay'] = [];
        $this->tokensByType['logic'] = [];
        $this->tokensByType['function'] = [];
        $this->tokensByType['projection'] = [];
        $this->tokensByType['pool'] = [];
        $this->tokensByType['feature'] = [];
        $this->constraintLinks = [];
        $this->constraintType = $this->regionNetwork->typeNetwork->constraintsByRegion[$baseRegion];
        $this->relaysByProjection = [];
        $this->projectionsByFeature = [];

    }

    public function addToken($token)
    {
        $this->tokens[] = $token;
        $this->classifyToken($token);
        //$token->region = $this->region;
    }

    public function classifyToken($token)
    {
        if ($token instanceof NodeRelay) {
            $token->phase = 'relay';
            $this->tokensByType['relay'][$token->typeNode->id][] = $token;
            foreach($token->inputSites as $site) {
                $this->relaysByProjection[$site->idSourceToken][] = $token->id;
            }
        } else if (($token instanceof NodeConstraint) && ($token->class == 'function')) {
            $token->phase = 'relay';
            $this->tokensByType['function'][] = $token;
        } else if (($token instanceof NodeConstraint) && ($token->class == 'logic')) {
            $token->phase = 'relay';
            $this->tokensByType['logic'][] = $token;
        } else if ($token->logic == 'N') {
            $token->phase = 'feature';
            $this->tokensByType['projection'][] = $token;
            foreach($token->inputSites as $site) {
                $this->projectionsByFeature[$site->idSourceToken][] = $token->id;
            }
        } else if ($token instanceof NodePool) {
            $this->tokensByType['pool'][] = $token;
            $token->phase = 'pool';
        } else if ($token instanceof NodeFeature) {
            $token->phase = 'feature';
            $this->tokensByType['feature'][] = $token;
        }
    }

    public function countTokens()
    {
        $this->dump('projections = ' . count($this->tokensByType['projection']));
        $this->dump('relays = ' . count($this->tokensByType['relay']));
        $this->dump('pools = ' . count($this->tokensByType['pool']));
        $this->dump('features = ' . count($this->tokensByType['feature']));
        //var_dump(count($this->tokensByType['logic']));
        //var_dump(count($this->tokensByType['function']));
        $this->dump('*************************************************************');
    }

    /*
     * Process
     */

    public function processProjection()
    {
        $count = count($this->tokensByType['projection']);
        if (count($this->tokensByType['projection'])) {
            foreach ($this->tokensByType['projection'] as $node) {
                $node->process();
            }
        }
        return $count;
    }

    public function processRelay()
    {
        $count = count($this->tokensByType['relay']);
        $this->dump('### processing relays');
        if (count($this->tokensByType['relay'])) {
            foreach ($this->tokensByType['relay'] as $idRelayType => $r) {
                foreach($r as $node) {
                    $node->activate();
                    $node->calculateO();
                    $node->activation = ++$this->tokenNetwork->activation;
                }
            }
            do {
                foreach ($this->tokensByType['relay'] as $idRelayType => $r) {
                    foreach($r as $node) {
                        $node->spreadToConstraint();
                    }
                }
                if (count($this->tokensByType['function'])) {
                    foreach ($this->tokensByType['function'] as $function) {
                        $function->process();
                    }
                }
                if (count($this->tokensByType['logic'])) {
                    foreach ($this->tokensByType['logic'] as $logic) {
                        $logic->process();
                    }
                }
                $switched = false;
                foreach ($this->tokensByType['relay'] as $idRelayType => $r) {
                    foreach($r as $node) {
                        $node->updateStatus();
                        $switched |= $node->switched;
                    }
                }
                $this->dump('switched = ' . ($switched ? 'true' : 'false'));
            } while ($switched);
            foreach ($this->tokensByType['relay'] as $idRelayType => $r) {
                foreach($r as $node) {
                    $node->spreadToPool();
                }
            }
        }
        return $count;
    }

    public function processPool()
    {
        $this->dump('### processing pools');
        $count = count($this->tokensByType['pool']);
        if (count($this->tokensByType['pool'])) {
            foreach ($this->tokensByType['pool'] as $node) {
                $node->process();
            }
        }
        return $count;
    }

    public function processFeature()
    {
        $next = [];
        $this->dump('### processing features');
        //$count = count($this->tokensByType['feature']);
        if (count($this->tokensByType['feature'])) {
            foreach ($this->tokensByType['feature'] as $node) {
                $nextNodes = $node->process();
                foreach($nextNodes as $nextNode) {
                    $next[] = $nextNode;
                }
            }
        }
        return $next;
    }

    public function updateConstraints()
    {
        if (count($this->tokensByType['relay'])) {
            foreach ($this->constraintType as $idRelay1 => $r2) {
                foreach ($r2 as $idRelay2 => $c) {
                    foreach ($c as $idConstraintType => $flag) {
                        foreach ($this->tokensByType['relay'][$idRelay1] as $nodeRelay1) {
                            foreach ($this->tokensByType['relay'][$idRelay2] as $nodeRelay2) {

                                if ($nodeRelay1->idParentToken == $nodeRelay2->idParentToken) {
                                    if (!isset($this->constraintToken[$nodeRelay1->id][$nodeRelay2->id][$idConstraintType])) {
                                        $constraintToken = $this->regionNetwork->createTokenById($idConstraintType);
                                        $this->addToken($constraintToken);
                                        $constraintToken->region = $this->region;
                                        $nodeRelay1->createLinkTo($constraintToken);
                                        $nodeRelay2->createLinkTo($constraintToken);
                                        $this->constraintToken[$nodeRelay1->id][$nodeRelay2->id][$idConstraintType] = $constraintToken;
                                    }
                                }

                            }
                        }
                    }
                }
            }
        }

    }

    public function backwardAddRelays(&$relays) {
        foreach($this->relaysByProjection as $idProjectionToken => $idRelayTokens) {
            foreach($idRelayTokens as $idRelayToken) {
                $relays[$idProjectionToken][] = $this->tokenNetwork->getNode($idRelayToken);
            }
        }
    }

    public function backwardAddProjections(&$projections) {
        foreach($this->projectionsByFeature as $idTokenFeature => $idProjectionTokens) {
            foreach($idProjectionTokens as $idProjectionToken) {
                $projections[$idTokenFeature][] = $this->tokenNetwork->getNode($idProjectionToken);
            }
        }
    }
}

