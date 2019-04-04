<?php

namespace Mesh\Element\Node;

use Mesh\Element\Structure\Slot;


class TokenNode extends Node
{
    public $tokenNetwork; // which network this token belongs to
    public $siteList; // shortcut for siteList from tokenNetwork
    public $index; // número sequencial do node dentro da tokenNetwork
    public $wordIndex;  // indice da palavra na sentença associada a este node - uso do head
    public $slots; // palavras associadas a este node
    public $activation; // número de ativação de nodes deste type
    public $status; // inactive, waiting, predictive, active, constrained, inhibited, exhausted
    public $typeNode; // typeNode associado a este tokenNode
    public $region; // region onde está este node
    public $layer; // layer onde este node está inserido
    public $phase; // phase do layer em que o node deve ser ativado ('relay', 'pool', 'feature');
    public $w; // node weight
    /* spread activation */
    public $thresold;
    public $a;
    public $o;
    public $energy;
    public $headSlot;
    public $depSlot;

    function __construct($id = '')
    {
        parent::__construct($id);
        $this->index = 0;
        $this->wordIndex = 0;
        $this->slots = new Slot(0);
        $this->activation = 0;
        $this->status = 'inactive';
        $this->phase = '';
        $this->typeNode = null;
        $this->region = '';
        $this->name = '';
        /* spread activation */
        $this->thresold = 0.2;
        $this->a = 0;
        $this->o = 0;
        $this->w = 1;
        $this->energy = 10;
        $this->headSlot = new Slot(0);
        $this->depSlot = new Slot(0);
    }

    public function __get($name)
    {
        if ($name == 'inputSites') {
            return $this->siteList->getInputSites($this->id);
        }
        if ($name == 'outputSites') {
            return $this->siteList->getOutputSites($this->id);
        }
        if ($name == 'sites') {
            return $this->siteList->getAllSites($this->id);
        }
        if (isset($this->typeNode->$name)) {
            return $this->typeNode->$name;
        }
    }

    public function setTokenNetwork($network)
    {
        $this->tokenNetwork = $network;
        $this->siteList = $network->siteList;
    }

    public function setIndex($index)
    {
        $this->index = $index;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function setWordIndex($index)
    {
        $this->wordIndex = $index;
    }

    public function getWordIndex()
    {
        return $this->wordIndex;
    }

    public function getSlots()
    {
        return $this->slots;
    }

    public function getSlotsStr()
    {
        return $this->slots->toString();
    }

    public function getA()
    {
        return $this->a;
    }

    public function setA($a)
    {
        $this->a = $a;
    }

    public function getO()
    {
        return $this->o;
    }

    public function setO($o)
    {
        $this->o = $o;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isActive()
    {
        return ($this->status == 'active');
    }

    public function energize()
    {
//        $this->energy = 5;
    }

    public function decay()
    {
        --$this->energy;
        if ($this->energy == 0) {
            $this->status = 'exhausted';
        }
    }

    public function setLayer($layer)
    {
        $this->layer = $layer;
    }

    public function getLayer()
    {
        return $this->layer;
    }

    public function process($ident = '')
    {
        $this->dump($ident . '*************************************************************');
        $this->dump($ident . '***** processing node ' . $this->name . '[' . $this->id . ']');
        $this->dump($ident . '*************************************************************');
        $nextNodes = [];
        if ($this->activate($ident)) {
            if ($this->canFire()) {
                $nextNodes = $this->fire($ident);
            }
        }
        $this->dump($ident . '***** end processing node ' . $this->name . '[' . $this->id . ']');
        return $nextNodes;
    }

    public function canFire()
    {
        return (($this->energy > 0) && ($this->status != 'inactive') && ($this->status != 'waiting'));
    }

    public function activate($ident = '')
    {
        $this->dump($ident . '%% activating node = ' . $this->getName() . '[' . $this->type . '] '
            . '[#input sites = ' . count($this->inputSites) . '] [#output sites = ' . count($this->outputSites) . ']'
            . '[layer = ' . $this->layer . '][phase = ' . $this->phase . ']'
            . '[logic = ' . $this->logic . '][status = ' . $this->status . ']');
        $oldA = $this->a;
        $inputSites = $this->inputSites;
        //$this->dump($inputSites);
        $this->a = 0;
        if (count($inputSites) > 0) {
            //$this->slots->setValue(0);
            foreach ($inputSites as $site) {
                if ($site->isInhibited()) {
                    $this->status = 'inhibited';
                }
            }
            if ($this->status != 'inhibited') {
                $a = 0;
                $w = 0;
                foreach ($inputSites as $site) {
                    if ($site->active()) {

                        $sourceToken = $this->tokenNetwork->getNode($site->idSourceToken);

                        if ($site->head()) {
                            $this->wordIndex = $sourceToken->wordIndex;
                            $this->idHead = $sourceToken->idHead;
                            $this->h = $sourceToken->h;
                            $this->d = $sourceToken->h;
                        } else {
                            $this->d = $sourceToken->h;
                        }
                        $this->slots->merge($sourceToken->getSlots());
                        $a += $site->a;
                        if ($site->w > $w) {
                            $w = $site->w;
                        }
                    }
                }
                $this->a += $a; // * peso do link = 1
                $this->w = $w;
                $this->updateStatus();
            }
        }
        if (($this->a == 0) || ($oldA == $this->a)) {
            $this->decay();
        }
        $this->dump($ident . '                 [a = ' . $this->a . '][windex = ' . $this->getWordIndex() . '][idHead = ' . $this->idHead . ']' . '][slots = ' . $this->getSlotsStr() . ':'
            . $this->getSlots()->getValue() . ']' . '[status = ' . $this->status . '][activation = ' . $this->activation . '][energy = ' . $this->energy . ']');
        return true;
    }

    public function calculateO()
    {
        //$sta = -1 * $this->a;
        //$this->o = (1 - exp(5 * $sta)) / (1 + exp(2 * $sta));
        $this->o = $this->a * $this->w;
    }

    public function fire($ident = '')
    {
        $this->calculateO();
        $next = [];
        $this->dump($ident . '%% firing node = ' . $this->getName() . '[o = ' . $this->o . '][energy = ' . $this->energy . '][type = ' . $this->type . '][process = ' . $this->process . ']');
        if ($this->o) {
            $this->dump($ident . '    [node ' . $this->getName() . ' status before spread = ' . $this->status . ']');
            $this->activation = ++$this->tokenNetwork->activation;
            $next = $this->spread($ident . '  ');
            $this->dump($ident . '    [node ' . $this->getName() . ' status after spread = ' . $this->status . ']');
        } else {
            $next[$this->id] = $this;
        }
        $this->dump($ident . 'next for = ' . $this->getName() . '--');
        foreach ($next as $id => $x) {
            $this->dump($ident . '    ' . $x->getName());
        }
        $this->dump($ident . '--');
        return $next;
    }

    public function spread($ident = '')
    {
        return [];
    }

    public function feedback($site, $ident = '')
    {
        $previousNode = $this->tokenNetwork->getNode($site->idSourceToken);
        $previousNode->process = 'feedback';
        $site->activateFeedback();
        $previousIsPool = ($previousNode->logic == 'O');
        if ($previousIsPool) {
            $previousNode->process('    ' . $ident);
        }
        return [];
    }

    public function feedforward($site, $ident = '')
    {
        $next = [];
        $site->activateForward();
        $nextNode = $this->propagate($site);
        $nextNode->process = 'feedforward';
        $nextNode->slots->merge($this->getSlots());
        $nextIsSameLayerPhase = ($nextNode->layer == $this->layer) && ($nextNode->phase == $this->phase);
        $nextIsProjection = ($nextNode->logic == 'N');
        $currentIsNotProjection = ($this->logic != 'N');
        //if ($nextIsProjection) {
            $next[] = $nextNode;
            ///if ($currentIsNotProjection) {
            //    $nextNode->process('    ' . $ident);
            //}
        //}
        return $next;
    }

    public function createLinkTo($nodeToken)
    {
        $site = $this->siteList->getOutputSiteFromTo($this, $nodeToken);
    }

    public function propagate($site, $ident = '')
    {
        $nextNode = $this->tokenNetwork->getNode($site->idTargetToken);
        $site->a = $this->o;
        $site->w = $this->w;
        $this->dump($ident . '** propagating: ' . $this->getName() . '[' . $this->status . ']:' . $site->id . ':' . $site->status . ' -> ' . $nextNode->getName() . '[' . $nextNode->status . ':' . $nextNode->layer . ':' . $nextNode->phase . ']:' . $nextNode->id);
        return $nextNode;
    }

    public function updateStatus()
    {
        if ($this->status != 'constrained') {
            if ($this->a < $this->thresold) {
                $this->status = 'inactive';
            }
        }
    }

    public function backwardPass()
    {

        $children = [];
        foreach ($this->inputSites as $sitePool) {
            $pool = $this->tokenNetwork->getNode($sitePool->idSourceToken);
            foreach ($pool->inputSites as $siteRelay) {
                $relay = $this->tokenNetwork->getNode($siteRelay->idSourceToken);
                foreach ($relay->inputSites as $siteProjection) {
                    $projection = $this->tokenNetwork->getNode($siteProjection->idSourceToken);
                    foreach ($projection->inputSites as $siteFeature) {
                        $feature = $this->tokenNetwork->getNode($siteFeature->idSourceToken);
                        $children[] = $feature;
                    }
                }
            }
        }
        return $children;
    }

}

