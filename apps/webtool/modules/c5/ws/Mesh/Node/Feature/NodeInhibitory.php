<?php
namespace Mesh\Node\Feature;

use Mesh\Element\Node\NodeFeature;

class NodeInhibitory extends NodeFeature
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
            $optional[$site->link->idSource] = true;
        }
        $isActive = false;
        $isWaiting = false;
        foreach ($this->inputSites as $site) {
            $input[$site->link->idSource] = true;
            $optional[$site->link->idSource] = $site->optional();
            $adjacentNodeStatus = ($site->active() ? $this->tokenNetwork->getNode($site->idSourceToken)->status : 'inactive');
            if ($site->label == 'rel_elementof') {
                if ($site->forward) {
                    $isActive = $isActive || ($adjacentNodeStatus == 'active') || ($adjacentNodeStatus == 'predictive');
                } else {
                    $isWaiting = $isWaiting || (!$site->optional());
                }
            } else if ($site->label == 'rel_projection') {
                $isActive = $isActive || $site->forward;
            }
        }
        $isActive = $isActive && (count($this->outputSites) > 0);
        $isComplete = true;
        foreach($input as $i => $c) {
            $isComplete = $isComplete && ($optional[$i] || $c);
        }
        $this->status = ((!$isComplete || $isWaiting) ? 'waiting' : ($isActive ? 'active' : 'predictive'));
    }

}

