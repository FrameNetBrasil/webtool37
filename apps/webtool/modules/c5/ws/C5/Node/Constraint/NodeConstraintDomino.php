<?php
namespace Mesh\Node\Constraint;

use Mesh\Element\Node\NodeConstraint;

class NodeConstraintDomino extends NodeConstraint
{
    public function evaluate($argument1Token, $argument2Token)
    {
        $argument1D = $argument1Token->d;
        $argument2H = $argument2Token->h;
        //$this->dump("constraint dominance  t1 = " . $argument1Token->id. ' t2 = ' . $argument2Token->id );
        $this->dump("constraint domino  a1 = " . $argument1D . ' a2 = ' . $argument2H );
        $result = ($argument2H == $argument1D);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }
}
