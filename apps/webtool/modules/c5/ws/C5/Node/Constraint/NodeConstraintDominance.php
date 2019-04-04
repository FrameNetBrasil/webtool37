<?php
namespace Mesh\Node\Constraint;

use Mesh\Element\Node\NodeConstraint;

class NodeConstraintDominance extends NodeConstraint
{
    public function evaluate($argument1Token, $argument2Token)
    {
        $argument1WordIndex = $argument1Token->wordIndex;
        $argument2IdHead = $argument2Token->idHead;
        //$this->dump("constraint dominance  t1 = " . $argument1Token->id. ' t2 = ' . $argument2Token->id );
        $this->dump("constraint dominance  a1 = " . $argument1WordIndex . ' a2 = ' . $argument2IdHead );
        $result = ($argument2IdHead == $argument1WordIndex);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }
}
