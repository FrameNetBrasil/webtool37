<?php
namespace Mesh\Node\Feature;

use Mesh\Element\Node\NodeFeature;


class NodeUDRelation extends NodeFeature
{

    public function spread($ident = '')
    {
        return $this->spreadBoth($ident);
    }

    public function updateStatus()
    {
        $input = [];
        $optional = [];
        foreach ($this->inputSites as $site) {
            $input[$site->link->idSource] = false;
            $optional[$site->link->idSource] = false;
        }
        //$this->dump($input);
        $isActive = false;
        $isWaiting = false;
        foreach ($this->inputSites as $site) {
            if ($site->forward) {
                $input[$site->link->idSource] = true;
                $optional[$site->link->idSource] = $site->optional();
                $adjacentNodeStatus = ($site->active() ? $this->tokenNetwork->getNode($site->idSourceToken)->status : 'inactive');
                if ($site->label == 'rel_elementof') {
                    $isActive = $isActive || ($adjacentNodeStatus == 'active') || ($adjacentNodeStatus == 'predictive');
                } else if ($site->label == 'rel_projection') {
                    $isActive = $isActive || $site->forward;
                }
            }
        }
        //$this->dump($input);
        $isComplete = true;
        foreach($input as $i => $c) {
            $isComplete = $isComplete && ($optional[$i] || $c);
        }
        //$this->dump('active = ' . ($isActive ? 'true' : 'false'));
        //$this->dump('waiting = ' . ($isWaiting ? 'true' : 'false'));
        //$this->dump('complete = ' . ($isComplete ? 'true' : 'false'));
        $this->status = ((!$isComplete || $isWaiting) ? 'waiting' : ($isActive ? 'active' : 'predictive'));
    }
}

