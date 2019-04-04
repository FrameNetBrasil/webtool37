<?php
namespace Mesh\Node\Feature;

use Mesh\Element\Node\NodeFeature;


class NodeDepRel extends NodeFeature
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
        $hasHead = $hasDep = false;
        foreach ($this->inputSites as $site) {
            if ($site->forward) {
                $input[$site->link->idSource] = true;
                $optional[$site->link->idSource] = $site->optional();
                $adjacentNodeStatus = ($site->active() ? $this->tokenNetwork->getNode($site->idSourceToken)->status : 'inactive');
                if ($site->label == 'rel_elementof') {
                    $a = ($adjacentNodeStatus == 'active') || ($adjacentNodeStatus == 'predictive');
                    $isActive = $isActive || $a;
                    if ($a) {
                        if ($site->head) {
                            $hasHead = true;
                        } else {
                            $hasDep = true;
                        }
                    }
                } else if ($site->label == 'rel_projection') {
                    $isActive = $isActive || $site->forward;
                    $isProjection = true;
                }
            }
        }
        //$this->dump($input);
        //$isComplete = true;
        //foreach($input as $i => $c) {
        //    $isComplete = $isComplete && ($optional[$i] || $c);
        //}
        if ($isProjection) {
            $isComplete = $isActive;
        } else {
            $isComplete = $hasHead && $hasDep;
        }
        //$this->dump('active = ' . ($isActive ? 'true' : 'false'));
        //$this->dump('waiting = ' . ($isWaiting ? 'true' : 'false'));
        //$this->dump('complete = ' . ($isComplete ? 'true' : 'false'));
        $this->status = ((!$isComplete || $isWaiting) ? 'waiting' : ($isActive ? 'active' : 'predictive'));
    }
}

