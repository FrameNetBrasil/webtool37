<?php
namespace Mesh\Node\Constraint;

use Mesh\Element\Node\NodeConstraint;

class NodeConstraintSame extends NodeConstraint
{
    public function evaluate($argument1Token, $argument2Token) {
        $argument1Slots = $argument1Token->getSlots()->getValue();
        $argument2Slots = $argument2Token->getSlots()->getValue();
        $this->dump("$$$$$$$  same " . $this->id . ' a1 = ' . $argument1Slots . '   a2 = ' . $argument2Slots);
        $result = (($argument2Slots & $argument1Slots) != 0);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }
}

