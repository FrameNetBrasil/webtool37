<?php
namespace C5\Node;

class NodeCE extends NodeC5
{


    public function spread($ident = '')
    {
        return $this->spreadForward($ident);
    }

    /*
    public function updateStatus()
    {
        $input = [];
        $optional = [];
        foreach ($this->inputSites as $site) {
            $input[$site->link->idSource] = false;
            $optional[$site->link->idSource] = true;
        }
        //$this->dump($input);
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
        //if ($isActive) {
        //    foreach ($this->outputSites as $site) {
        //        $isActive = $isActive && $site->active();
        //    }
        //}
        //$this->dump($input);
        $isComplete = true;
        foreach($input as $i => $c) {
            $isComplete = $isComplete && ($optional[$i] || $c);
        }
//        $this->dump('active = ' . ($isActive ? 'true' : 'false'));
//        $this->dump('waiting = ' . ($isWaiting ? 'true' : 'false'));
//        $this->dump('complete = ' . ($isComplete ? 'true' : 'false'));
        $this->status = ((!$isComplete || $isWaiting) ? 'waiting' : ($isActive ? 'active' : 'predictive'));
    }
    */

}

