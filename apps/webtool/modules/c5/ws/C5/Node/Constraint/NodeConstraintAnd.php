<?php
namespace Mesh\Node\Constraint;

use Mesh\Element\Node\NodeConstraint;

class NodeConstraintAnd extends NodeConstraint
{
    public function evaluate($argument1Token, $argument2Token) {
        $argument1Active = ($argument1Token->status == 'active') || ($argument1Token->status == 'predictive');
        $argument2Active = ($argument2Token->status == 'active') || ($argument2Token->status == 'predictive');
        $this->dump("constraint and  t1 = " . $argument1Token->id. ' t2 = ' . $argument2Token->id );
        $this->dump("constraint and  a1 = " . ($argument1Active?'true':'false') . '   a2 = ' . ($argument2Active?'true':'false'));
        $result = ($argument1Active && $argument2Active);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }

}

