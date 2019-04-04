<?php
namespace Mesh\Element\Node;

class NodeConstraint extends TokenNode
{
    public $constraint;
    public $constraints = [
        'after' => 'function',
        'before' => 'function',
        'side' => 'function',
        'meets' => 'function',
        'follows' => 'function',
        'different' => 'function',
        'same' => 'function',
        'dominance' => 'function',
        'domino' => 'function',
        'head' => 'function',
        'iso' => 'function',
        'hasword' => 'function',
        'and_' => 'logic',
        'xor_' => 'logic',
        'near' => 'logic'
    ];

    public function setParams($params)
    {
        parent::setParams($params);
        $this->setConstraint($params['class']);
    }

    public function setConstraint($constraint) {
        if (($constraint == 'and') || ($constraint == 'xor')) {
            $constraint = $constraint . '_';
        }
        $this->constraint = $constraint;
        $this->class = $this->constraints[$constraint];
    }

    public function getConstraint() {
        $constraint = $this->constraint;
        if (($constraint == 'and_') || ($constraint == 'xor_')) {
            $constraint = substr($constraint, -1);
        }
        return $constraint;
    }

    public function canFire()
    {
        return (($this->energy > 0) && ($this->status != 'inactive') && ($this->status != 'waiting'));
    }

    /*
     * O spread de um node constraint é simplesmente o feedback para o node relay argument2
     */
    public function spread($ident = '')
    {
        $next = [];
        return $next;
    }


    public function updateStatus()
    {
        $this->status = 'waiting';
        $argument1Token = NULL;
        foreach ($this->inputSites as $site) {
            if (!$site->forward) {
                continue;
            }
            if ($site->label == 'rel_argument1') {
                $argument1Token = $this->tokenNetwork->getNode($site->idSourceToken);
                $this->status = 'active';
                break;
            }
        }
        if ($argument1Token) {
            foreach ($this->inputSites as $site) {
                if (!$site->forward) {
                    continue;
                }
                if ($site->label == 'rel_argument2') {
                    $argument2Token = $this->tokenNetwork->getNode($site->idSourceToken);
                    $result = $this->evaluate($argument1Token, $argument2Token);
                    $this->status = ($result ? 'active' : 'constrained');
                }
            }
        }
        $this->dump('==== constraint status: ' . $this->status);
    }

    public function evaluate($argument1Token, $argument2Token) {
        $constraint = $this->constraint;
        return $this->$constraint($argument1Token, $argument2Token);
    }

    public function after($argument1Token, $argument2Token)
    {
        // result = arg1 after arg2
        $argument1WordIndex = $argument1Token->getSlots()->min();
        $argument2WordIndex = $argument2Token->getSlots()->max();
        $result = ($argument1WordIndex > $argument2WordIndex);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function and_($argument1Token, $argument2Token) {
        $argument1Active = ($argument1Token->status == 'active') || ($argument1Token->status == 'predictive');
        $argument2Active = ($argument2Token->status == 'active') || ($argument2Token->status == 'predictive');
        $this->dump("constraint and  t1 = " . $argument1Token->id. ' t2 = ' . $argument2Token->id );
        $this->dump("constraint and  a1 = " . ($argument1Active?'true':'false') . '   a2 = ' . ($argument2Active?'true':'false'));
        $result = ($argument1Active && $argument2Active);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function before($argument1Token, $argument2Token)
    {
        // result = arg1 before arg2
        $argument1WordIndex = $argument1Token->getSlots()->max();
        $argument2WordIndex = $argument2Token->getSlots()->min();
        $result = ($argument1WordIndex < $argument2WordIndex);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function side($argument1Token, $argument2Token)
    {
        // result = arg1 side arg2
        $argument1WordIndex = $argument1Token->wordIndex;
        $argument2WordIndex = $argument2Token->wordIndex;
        $result = (abs($argument1WordIndex - $argument2WordIndex) == 1);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function different($argument1Token, $argument2Token) {
        $argument1Slots = $argument1Token->getSlots()->getValue();
        $argument2Slots = $argument2Token->getSlots()->getValue();
        $this->dump("$$$$$$$  different " . $this->id . ' a1 = ' . $argument1Slots . '   a2 = ' . $argument2Slots);
        $result = (($argument2Slots & $argument1Slots) == 0);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function dominance($argument1Token, $argument2Token)
    {
        $argument1WordIndex = $argument1Token->wordIndex;
        $argument2IdHead = $argument2Token->idHead;
        //$this->dump("constraint dominance  t1 = " . $argument1Token->id. ' t2 = ' . $argument2Token->id );
        $this->dump("constraint dominance  a1 = " . $argument1WordIndex . ' a2 = ' . $argument2IdHead );
        $result = ($argument2IdHead == $argument1WordIndex);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function domino($argument1Token, $argument2Token)
    {
        $argument1D = $argument1Token->d;
        $argument2H = $argument2Token->h;
        //$this->dump("constraint dominance  t1 = " . $argument1Token->id. ' t2 = ' . $argument2Token->id );
        $this->dump("constraint domino  a1 = " . $argument1D . ' a2 = ' . $argument2H );
        $result = ($argument2H == $argument1D);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function follows($argument1Token, $argument2Token)
    {
        // result = arg1 follows arg2
        $argument1WordIndex = $argument1Token->getSlots()->max();
        $argument2WordIndex = $argument2Token->getSlots()->min();
        $result = (($argument1WordIndex - 1) == $argument2WordIndex);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function hasword($argument1Token, $argument2Token)
    {
        $this->dump('constraint hasword  a1 = ' . $argument1Token->wordIndex . ' a2 = ' . $argument2Token->getSlotsStr());
        $result = (($argument2Token->getSlots()->get($argument1Token->wordIndex)) != 0);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function head($argument1Token, $argument2Token)
    {
        $argument1H = $argument1Token->h;
        $argument2H = $argument2Token->h;
        $this->dump("constraint head  a1 = " . $argument1H . ' a2 = ' . $argument2H );
        $result = ($argument2H == $argument1H);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function iso($argument1Token, $argument2Token)
    {
        $argument1Slots = $argument1Token->getSlots()->getValue();
        $argument2Slots = $argument2Token->getSlots()->getValue();
        $andArg = ($argument2Slots & $argument1Slots);
        $headBin = (1 << $argument1Token->h);
        $argument1H = $argument1Token->h;
        $argument2H = $argument2Token->h;
        $this->dump("constraint head  a1 = " . $argument1H . ' a2 = ' . $argument2H );
        $result = ($argument2H == $argument1H) && ($andArg == $headBin);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function meets($argument1Token, $argument2Token)
    {
        // result = arg1 meets arg2
        $argument1WordIndex = $argument1Token->getSlots()->max();
        $argument2WordIndex = $argument2Token->getSlots()->min();
        $result = (($argument1WordIndex + 1) == $argument2WordIndex);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function near($argument1Token, $argument2Token) {
        $argument1WordIndex = $argument1Token->wordIndex;
        $argument2WordIndex = $argument2Token->wordIndex;
        $w = 1 - (abs($argument1WordIndex - $argument2WordIndex) / $argument1WordIndex);
        $argument2Token->w = $argument2Token->w * $w;
        return true;
    }

    public function same($argument1Token, $argument2Token) {
        $argument1Slots = $argument1Token->getSlots()->getValue();
        $argument2Slots = $argument2Token->getSlots()->getValue();
        $this->dump("$$$$$$$  same " . $this->id . ' a1 = ' . $argument1Slots . '   a2 = ' . $argument2Slots);
        $result = (($argument2Slots & $argument1Slots) != 0);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

    public function xor_($argument1Token, $argument2Token) {
        $argument1Active = ($argument1Token->status == 'active') || ($argument1Token->status == 'predictive');
        $argument2Active = ($argument2Token->status == 'active') || ($argument2Token->status == 'predictive');
        $result = ($argument1Active && $argument2Active);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return ($argument1Token->status != 'active');
    }
}

