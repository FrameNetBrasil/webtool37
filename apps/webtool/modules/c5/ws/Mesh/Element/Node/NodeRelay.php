<?php
namespace Mesh\Element\Node;

class NodeRelay extends TokenNode
{
    public $switched;
    public $idPoolToken;
    public $idChildToken;
    public $idParentToken;

    public function canFire()
    {
        return (($this->energy > 0) && (($this->status == 'active') || ($this->status == 'predictive') || ($this->status == 'fired')));
    }

    public function spreadToConstraint($ident = '') {
        if ($this->o > 0) {
            foreach ($this->outputSites as $site) {
                if (($site->label != 'rel_argument1') && ($site->label != 'rel_argument2')) {
                    continue;
                }
                $this->dump('spreading to constraint from site [' . $site->id . ' ' . $site->label . ' ' . $site->status . ' ' . $site->type . ' ' . $site->idTargetToken . ($this->switched ? ' switched ' : '') . ']');
                $this->feedforward($site, $ident);
            }
        }
    }

    public function spreadToPool($ident = '') {
        if ($this->o > 0) {
            foreach ($this->outputSites as $site) {
                if (($site->label == 'rel_argument1') || ($site->label == 'rel_argument2')) {
                    continue;
                }
                // só pode fazer forward para os pools se estiver ativo
                if ($this->status != 'active') {
                    continue;
                }
                $this->dump($ident . 'spreading to pool from site [' . $site->id . ' ' . $site->label . ' ' . $site->status . ' ' . $site->type . ' ' . $site->idTargetToken . ']');
                $this->feedforward($site, $ident);
            }
        }
    }

    /*
    public function spread($ident = '')
    {
        $next = [];
        // processa primeiro as constraints deste relay
        foreach ($this->outputSites as $site) {
            if (($site->label != 'rel_argument1') && ($site->label != 'rel_argument2')) {
                continue;
            }
            // se já fez o forward antes, verifica se o processo atual é de feedback
            // se for feedback, só faz o forward de novo se houve algum switch
            if ($site->forward) {
                if ($this->process == 'feedback') {
                    if (!$this->switched) {
                        continue;
                    }
                }
            }
            $this->dump($ident . 'spreading to constraint from site [' . $site->id . ' ' . $site->label . ' ' . $site->status . ' ' . $site->type . ' ' . $site->idTargetToken . ($this->switched ? ' switched ' : '') . ']');
            $nextNodes = $this->feedforward($site, $ident);
            foreach ($nextNodes as $nextNode) {
                $next[$nextNode->id] = $nextNode;
            }
        }
        foreach ($this->outputSites as $site) {
            // as constraints foram processadas antes
            if (($site->label == 'rel_argument1') || ($site->label == 'rel_argument2')) {
                continue;
            }
            // só pode fazer forward para os pools se estiver ativo
            if ($this->status != 'active') {
                continue;
            }
            $this->dump($ident . 'spreading from site [' . $site->id . ' ' . $site->label . ' ' . $site->status . ' ' . $site->type . ' ' . $site->idTargetToken . ']');
            $nextNodes = $this->feedforward($site, $ident);
            foreach ($nextNodes as $nextNode) {
                $next[$nextNode->id] = $nextNode;
            }
        }
        return $next;
    }
    */

    public function calculateO()
    {
        $this->o = $this->a;
    }

    public function updateStatus()
    {
        $this->dump('relay - updating status');
        $actualStatus = $this->status;
        $this->dump('---actual status = ' . $actualStatus);
        if ($this-> a == 0) {
            $this->status == 'inactive';
        } else {
            $isActive = true;//($this->status == 'active');
            $isWaiting = false;//($this->status == 'waiting');
            $isConstrained = false;//($this->status == 'constrained');
            //
            $isActive = $isActive && ($this->w > 0);
            $isConstrained = ($this->w == 0);
            /*
            foreach ($this->outputSites as $site) {
                //$this->dump($site);
                if ($site->label == 'rel_argument2') {
                    $constraintNodeToken = $this->tokenNetwork->getNode($site->idTargetToken);
                    //$constraintSite = $this->siteList->getInputSiteFromTo($constraintNodeToken, $this);
                    $isActive = $isActive && $site->feedback;
                    $isWaiting = $isWaiting || ($constraintNodeToken->status == 'waiting');
                    $isConstrained = $isConstrained || ($site->feedback && ($constraintNodeToken->status == 'constrained'));
                }
            }

            foreach ($this->inputSites as $site) {
                $isActive = $isActive && $site->forward;
            }
            */
            $this->status = ($isConstrained ? 'constrained' : ($isWaiting ? 'waiting' : ($isActive ? 'active' : 'predictive')));
        }
        $this->dump('---updated status = ' . $this->status);
        $this->switched = ($this->status != $actualStatus);
        //parent::updateStatus();
    }

}

