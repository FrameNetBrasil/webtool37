<?php
namespace Mesh\Element\Node;


class NodeFeature extends TokenNode
{

    public function canFire()
    {
        return (($this->energy > 0) && (($this->status == 'active') || ($this->status == 'fired')));
    }

    protected function spreadForward($ident = '')
    {
        $next = [];
        foreach ($this->outputSites as $site) {
            //if ($site->forward) {
            //    continue;
            //}
            $this->dump($ident . 'spreading from site [' . $site->id . ' ' . $site->label . ' ' . $site->status . ' ' . $site->type . ' ' . $site->idATargetToken . ']');
            $nextNodes = $this->feedforward($site, $ident);
            foreach ($nextNodes as $nextNode) {
                $next[$nextNode->id] = $nextNode;
            }
        }
        return $next;
    }

    protected function spreadBack($ident = '')
    {
        $next = [];
        foreach ($this->inputSites as $site) {
            if (!$site->forward) { // só tem feedback se teve forward
                continue;
            }
            if ($site->label == 'rel_constraint') {
                continue;
            }
            if ($site->label == 'rel_projection') {
                continue;
            }
            $this->dump($ident . 'feedback to input site ' . $site->id . ' ' . $site->label . ' ' . $site->status . ' ' . $site->idSourceToken);
            $nextNodes = $this->feedback($site, $ident);
            foreach ($nextNodes as $nextNode) {
                $next[$nextNode->id] = $nextNode;
            }
        }
        return $next;
    }

    public function spreadBoth($ident = '')
    {
        foreach ($this->spreadBack($ident) as $nextNode) {
            $next[$nextNode->id] = $nextNode;
        }
        foreach ($this->spreadForward($ident) as $nextNode) {
            $next[$nextNode->id] = $nextNode;
        }
        return $next;
    }


    public function spread($ident = '') {
        return $this->spreadForward($ident);
    }

    public function inheritance($ident = '')
    {
    }

    public function updateStatus()
    {
        $this->status = 'active';
        parent::updateStatus();
        $isActive = ($this->status == 'active');// && (count($this->outputSites) > 0);
        //if ($isActive) {
        //    foreach ($this->outputSites as $site) {
        //        $isActive = $isActive && $site->active();
        //    }
        //}
        $this->status = ($isActive ? 'active' : 'predictive');
    }


}

