<?php
namespace Mesh\Element\Node;


class NodePool extends TokenNode
{
    public $switched;

    public function canFire()
    {
        return (($this->energy > 0) && (($this->status == 'active') || ($this->status == 'predictive') || ($this->status == 'constrained') || ($this->status == 'fired')));
    }

    public function spread($ident = '')
    {
        $next = [];
        foreach ($this->outputSites as $site) {
            //if ($site->forward && !$this->switched) {
            //    continue;
            //}
            if (($site->label == 'rel_inheritance') && ($this->status != 'active')) {
                continue;
            }
            if (($site->label == 'rel_projection') && ($this->status != 'active')) {
                continue;
            }
            $this->dump($ident . 'spreading from site [' . $site->id . ' ' . $site->label . ' ' . $site->status . ' ' . $site->type . ' ' . $site->idAdjacentNodeToken . ']');
            $nextNodes = $this->feedforward($site, $ident);
            foreach ($nextNodes as $nextNode) {
                $next[$nextNode->id] = $nextNode;
            }
        }
        return $next;
    }

    public function updateStatus()
    {
        $actualStatus = $this->status;

        $isActive = $isWaiting = $isFeedback = false;
        if ($this-> a == 0) {
            $this->status == 'inactive';
        } else {

            foreach ($this->inputSites as $site) {
                if (($site->label == 'rel_relay') || ($site->label == 'rel_projection')) {
                    $isActive = $isActive || $site->forward;// && ($adjacentNodeStatus == 'active')  && ($adjacentNodeType == 'relay'));
                } else if ($site->label == 'rel_inheritance_cxn') {
                    $isWaiting = $isWaiting || ($site->forward && !$site->active() && !$site->predictive());
                }
            }
            foreach ($this->outputSites as $site) {
                $adjacentNodeStatus = $this->tokenNetwork->getNode($site->idTargetToken)->status;
                $isFeedback = $isFeedback || ($site->feedback && ($adjacentNodeStatus == 'active'));
            }
            $this->status = ($isFeedback ? 'active' : ($isWaiting ? 'waiting' : ($isActive ? 'active' : 'predictive')));
        }
        $this->switched = ($this->status != $actualStatus);
        //parent::updateStatus();
    }


}

