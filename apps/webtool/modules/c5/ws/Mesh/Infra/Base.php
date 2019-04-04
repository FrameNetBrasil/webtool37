<?php
namespace Mesh\Infra;

/**
 * Class Base
 * Classe herdada por todas as demais classes do CARMA (exceto Manager)
 * Possui acesso ao Manager e ao Log
 * @package Carma\Infra
 */
class Base
{
    public $manager;
    public $container;

    public function __construct()
    {
    }

    public function setManager($manager)
    {
        $this->manager = $manager;
        $this->container = $this->manager->getContainer();
    }

    public function dump($msg) {
        $this->manager->dump($msg);
    }

}

