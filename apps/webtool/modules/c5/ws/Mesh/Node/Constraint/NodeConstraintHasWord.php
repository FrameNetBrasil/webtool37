<?php
namespace Mesh\Node\Constraint;

use Mesh\Element\Node\NodeConstraint;

class NodeConstraintHasWord extends NodeConstraint
{
    public function evaluate($argument1Token, $argument2Token)
    {
        $this->dump('constraint hasword  a1 = ' . $argument1Token->wordIndex . ' a2 = ' . $argument2Token->getSlotsStr());
        $result = (($argument2Token->getSlots()->get($argument1Token->wordIndex)) != 0);
        $argument2Token->w *= ($result ? 1.0 : 0.0);
        return $result;
    }
}
