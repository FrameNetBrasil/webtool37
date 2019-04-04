<?php
namespace Mesh\Node\Feature;

use Mesh\Element\Node\NodeFeature;

class NodeWord extends NodeFeature
{
    public function __construct($id) {
        parent::__construct($id);
        $this->a = 1;
    }

    public function activate($ident = '')
    {
        parent::activate($ident);
        $this->status = 'active';
        return true;
    }

    public function calculateO()
    {
        $this->o = 1;
    }

    public function spread($ident = '')
    {
        $next = parent::spread($ident);
        return $next;
    }

}

