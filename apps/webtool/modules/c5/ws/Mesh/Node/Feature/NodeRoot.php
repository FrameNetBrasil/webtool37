<?php
namespace Mesh\Node\Feature;

use Mesh\Element\Node\NodeFeature;

class NodeRoot extends NodeFeature
{
    public function updateStatus()
    {
        //$this->status = 'active';
        parent::updateStatus();
    }

}

