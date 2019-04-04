<?php
namespace Mesh\Node\Feature;

use Mesh\Element\Node\NodeFeature;

class NodeFrame extends NodeFeature
{

    public function spread($ident = '')
    {
        return $this->spreadBoth($ident);
    }

    /*
    public function updateStatus()
    {
        $this->dump('--- frame updateStatus : ' . $this->id);
        $input = [];
        $optional = [];
        foreach ($this->inputSites as $site) {
            $input[$site->link->idSource] = false;
            $optional[$site->link->idSource] = true;
        }
        $isActive = false;
        $isWaiting = false;
        $hasTarget = false;
        foreach ($this->inputSites as $site) {
            if ($site->forward) {
                $this->dump('input = ' . $site->idSourceToken . ' - ' . ($site->link->head ? 'is head' : 'no head'));
                $input[$site->link->idSource] = $site->forward;
                $optional[$site->link->idSource] = $site->optional();
                $adjacentNodeStatus = ($site->active() ? $this->tokenNetwork->getNode($site->idSourceToken)->status : 'inactive');
                if ($site->label == 'rel_elementof') {
                    $isActive = $isActive || ($adjacentNodeStatus == 'active') || ($adjacentNodeStatus == 'predictive');
                    $hasTarget = $hasTarget || ($site->link->head && (($adjacentNodeStatus == 'active') || ($adjacentNodeStatus == 'predictive')));
                } else if ($site->label == 'rel_projection') {
                    $isActive = $isActive || $site->forward;
                }
            }
        }
        //$this->dump($input);
        $isComplete = true;
        foreach ($input as $i => $c) {
            $isComplete = $isComplete && ($optional[$i] || $c);
        }
        $this->dump('active = ' . ($isActive ? 'true' : 'false'));
        $this->dump('waiting = ' . ($isWaiting ? 'true' : 'false'));
        $this->dump('complete = ' . ($isComplete ? 'true' : 'false'));
        $this->dump('hasTarget = ' . ($hasTarget ? 'true' : 'false'));
        $this->status = ($hasTarget ? 'active' : ((!$isComplete || $isWaiting) ? 'waiting' : ($isActive ? 'active' : 'predictive')));
    }
    */

}

