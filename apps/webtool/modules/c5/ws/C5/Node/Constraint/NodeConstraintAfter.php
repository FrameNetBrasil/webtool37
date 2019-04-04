<?php
namespace Mesh\Node\Constraint;

use Mesh\Element\Node\NodeConstraint;

class NodeConstraintAfter extends NodeConstraint
{
    public function evaluate($argument1Token, $argument2Token)
    {
        // result = arg1 after arg2
        $argument1WordIndex = $argument1Token->getSlots()->min();
        $argument2WordIndex = $argument2Token->getSlots()->max();
        $result = ($argument1WordIndex > $argument2WordIndex);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }
}
