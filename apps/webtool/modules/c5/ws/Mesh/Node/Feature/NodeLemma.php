<?php
namespace Mesh\Node\Feature;

use Mesh\Element\Node\NodeFeature;

class NodeLemma extends NodeFeature
{
    public function spread($ident = '')
    {
        return $this->spreadBoth($ident);
    }

    public function updateStatus()
    {
        $input = [];
        $optional = [];
        foreach ($this->sites as $site) {
            if ($site->input()) {
                $input[$site->idAdjacentNodeType] = false;
                $optional[$site->idAdjacentNodeType] = true;
            }
        }
        $isActive = false;
        $isWaiting = false;
        foreach ($this->sites as $site) {
            $input[$site->idAdjacentNodeType] = true;
            $optional[$site->idAdjacentNodeType] = $site->optional();
            $adjacentNodeStatus = ($site->active() ? $this->getAdjacentNodeToken($site)->status : 'inactive');
            if ($site->label == 'rel_elementof') {
                if ($site->in) {
                    $isActive = $isActive || ($adjacentNodeStatus == 'active') || ($adjacentNodeStatus == 'predictive');
                } else {
                    $isWaiting = $isWaiting || (!$site->optional());
                    //$isWaiting = $isWaiting || $site->head();
                }
            } else if ($site->label == 'rel_constraint') {
                if ($site->in) {
                    $isActive = $isActive || ($adjacentNodeStatus == 'active') || ($adjacentNodeStatus == 'predictive');
                } else {
                    $isWaiting = $isWaiting || (!$site->optional());
                }
            } else if ($site->label == 'rel_projection') {
                $isActive = $isActive || $site->in();
            }
        }
        $isComplete = true;
        foreach($input as $i => $c) {
            $isComplete = $isComplete && ($optional[$i] || $c);
        }
        $this->status = ((!$isComplete || $isWaiting) ? 'waiting' : ($isActive ? 'active' : 'predictive'));
        parent::updateStatus();
    }

}

