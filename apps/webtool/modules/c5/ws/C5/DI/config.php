<?php
use DI\Factory\RequestedEntry;
use function DI\create;
use C5\ORM\EntityManager;
use C5\Infra\Manager;


return [
    'Mesh\Infra\Logger' => DI\create()
        ->constructor('C5Log'),
    Manager::class => function(\Psr\Container\ContainerInterface $c) {
        $manager = Manager::getInstance();
        $manager->setLogger($c->get('Mesh\Infra\Logger'));
        $manager->setContainer($c);
        return $manager;
    },
    'C5\ORM\Service\GraphService' => create()
        ->constructor(EntityManager::getInstance('c5')),
    'C5\ORM\Service\C5Service' => create()
        ->constructor(EntityManager::getInstance('c5')),
    'C5\Infra\GraphViz' => create()
        ->constructor(),
    'ResourceFull' => create('C5\Service\ResourceFull')
        ->method('setManager', DI\get(Manager::class))
        ->method('setDataService', DI\get('C5\ORM\Service\C5Service'))
        ->method('setGraphService', DI\get('C5\ORM\Service\GraphService')),

    'FullNetwork' => create('C5\Network\FullNetwork')
        ->method('setManager', DI\get(Manager::class))
        ->method('setGraphViz', DI\get('C5\Infra\GraphViz'))
        ->method('setGraphService', DI\get('C5\ORM\Service\GraphService')),
    'TypeNetwork' => create('Mesh\Element\Network\TypeNetwork')
        ->method('setManager', DI\get(Manager::class))
        ->method('setMeshService', DI\get('C5\ORM\Service\MeshService'))
        ->method('setGraphService', DI\get('C5\ORM\Service\GraphService')),
    'TokenNetwork' => create('Mesh\Element\Network\TokenNetwork')
        ->method('setGraphViz', DI\get('C5\Infra\GraphViz'))
        ->method('setManager', DI\get(Manager::class)),
    'ConceptNetwork' => create('C5\Network\ConceptNetwork')
        ->method('setGraphViz', DI\get('C5\Infra\GraphViz'))
        ->method('setManager', DI\get(Manager::class)),
    'RegionNetwork' => create('Mesh\Element\Network\RegionNetwork')
        ->method('setGraphViz', DI\get('C5\Infra\GraphViz'))
        ->method('setManager', DI\get(Manager::class)),
    //'NodeCxn' => create('C5\Node\NodeCxn')
    //    ->constructor(DI\get('idNode')),
    'NodeCxn' => function() {
        return new C5\Node\NodeCxn();
    },
    'NodeCE' => function() {
        return new C5\Node\NodeCE();
    },
    'NodeFrame' => function() {
        return new C5\Node\NodeFrame();
    },
    'NodeUDRelation' => function() {
        return new C5\Node\NodeUDRelation();
    },
    'NodeConcept' => function() {
        return new C5\Node\NodeConcept();
    },
    'NodeConstraint' => function() {
        return new C5\Node\NodeConstraint();
    },

];
