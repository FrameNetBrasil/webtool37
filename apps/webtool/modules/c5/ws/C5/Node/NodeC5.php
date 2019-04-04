<?php
namespace C5\Node;


class NodeC5 extends \Mesh\Element\Node\TokenNode
{
    public $isQuery;

    public function __construct() {
        parent::__construct();
        $this->isQuery = false;
    }

    public function activate($ident = '')
    {
        $this->dump($ident . '%% activating node = ' . $this->getName() . '[' . $this->type . '] '
            . '[#input sites = ' . count($this->inputSites) . '] [#output sites = ' . count($this->outputSites) . '][status = ' . $this->status . ']');
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
        $this->dump($ident . '                 [a = ' . $this->a . '][status = ' . $this->status . '][activation = ' . $this->activation . '][energy = ' . $this->energy . ']');
        return true;
    }

    public function calculateO()
    {
        $sta = -1 * $this->a;
        $this->o = (1 - exp(5 * $sta)) / (1 + exp(2 * $sta));
        //$this->a = $this->o;
        //$this->o = $this->a * $this->w;
    }

    public function fire($ident = '')
    {
        $this->calculateO();
        $next = [];
        $this->dump($ident . '%% firing node = ' . $this->getName() . '[o = ' . $this->o . '][energy = ' . $this->energy . '][type = ' . $this->type . ']');
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

    public function feedback($site, $ident = '')
    {
        $previousNode = $this->tokenNetwork->getNode($site->idSourceToken);
        $previousNode->process = 'feedback';
        $site->activateFeedback();
        //$previousIsPool = ($previousNode->logic == 'O');
        //if ($previousIsPool) {
        //    $previousNode->process('    ' . $ident);
        //}
        return [];
    }

    public function feedforward($site, $ident = '')
    {
        $next = [];
        $site->activateForward();
        $nextNode = $this->propagate($site);
        $nextNode->process = 'feedforward';
        //$nextNode->slots->merge($this->getSlots());
        //$nextIsSameLayerPhase = ($nextNode->layer == $this->layer) && ($nextNode->phase == $this->phase);
        //$nextIsProjection = ($nextNode->logic == 'N');
        //$currentIsNotProjection = ($this->logic != 'N');
        //if ($nextIsProjection) {
        $next[] = $nextNode;
        ///if ($currentIsNotProjection) {
        //    $nextNode->process('    ' . $ident);
        //}
        //}
        return $next;
    }

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

