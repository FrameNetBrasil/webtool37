<?php
/**
 * Created by PhpStorm.
 * User: ematos
 * Date: 19/07/2018
 * Time: 10:27
 */

namespace C5\ORM\Service;

class C5Service extends BaseService
{
    public function clear() {
    }

    public function getAllNodes() {
        $cmd = "
select Entity.idEntity, name, type
from Entity
join Construction on (Entity.idEntity = Construction.idEntity)
join Entry on (Construction.entry = Entry.entry)
where type IN ('CX') and (Entry.idLanguage = 0)
UNION
select Entity.idEntity, name, type
from Entity
join Frame on (Entity.idEntity = Frame.idEntity)
join Entry on (Frame.entry = Entry.entry)
where type IN ('FR') and (Entry.idLanguage = 0)
UNION
select Entity.idEntity, name, type
from Entity
join ConstructionElement on (Entity.idEntity = ConstructionElement.idEntity)
join Entry on (ConstructionElement.entry = Entry.entry)
where type IN ('CE') and (Entry.idLanguage = 0)
UNION
select Entity.idEntity, concat('ud_', info) name, type
from Entity
join UDRelation on (Entity.idEntity = UDRelation.idEntity)
where type IN ('UR')
UNION
select Entity.idEntity, name, type
from Entity
join Concept on (Entity.idEntity = Concept.idEntity)
join Entry on (Concept.entry = Entry.entry)
where type IN ('CP') and (Entry.idLanguage = 0)
UNION
select Entity.idEntity, alias, type
from Entity
where type IN ('CN')
        ";
        $result = $this->query($cmd);
        return $result;
    }

    public function getAllLinks(){
        $cmd = "
select idEntity1 idSource, idEntity2 idTarget, relationType
from view_relation 
where relationType = 'rel_evokes'
and (idEntity1 in (select idEntity from C5Node))
and (idEntity2 in (select idEntity from C5Node))
UNION
select idEntity1 idSource, idEntity2 idTarget, relationType
from view_relation 
where relationType = 'rel_elementof'
and (idEntity1 in (select idEntity from C5Node))
and (idEntity2 in (select idEntity from C5Node))
UNION
select idEntity1 idSource, idEntity2 idTarget, relationType
from view_relation 
where relationType = 'rel_subtypeof'
and (idEntity1 in (select idEntity from C5Node))
and (idEntity2 in (select idEntity from C5Node))
UNION
select idEntity2 idSource, idEntity3 idTarget, relationType
from view_relation 
where relationType = 'rel_constraint_udrelation'
and (idEntity2 in (select idEntity from C5Node))
and (idEntity3 in (select idEntity from C5Node))
UNION
select idEntity2 idSource, idEntity3 idTarget, relationType
from view_relation 
where relationType = 'rel_constraint_cxn'
and (idEntity2 in (select idEntity from C5Node))
and (idEntity3 in (select idEntity from C5Node))
UNION
select idEntity2 idSource, idEntity3 idTarget, relationType
from view_relation 
where relationType = 'rel_constraint_constraint'
and (idEntity2 in (select idEntity from C5Node))
and (idEntity3 in (select idEntity from C5Node))
UNION
select idEntity2 idSource, idEntity1 idTarget, relationType
from view_relation 
where relationType = 'rel_constraint_before'
and (idEntity1 in (select idEntity from C5Node))
and (idEntity2 in (select idEntity from C5Node))
UNION
select idEntity3 idSource, idEntity1 idTarget, relationType
from view_relation 
where relationType = 'rel_constraint_before'
and (idEntity1 in (select idEntity from C5Node))
and (idEntity3 in (select idEntity from C5Node))
        ";
        $result = $this->query($cmd);
        return $result;
    }

    //////////////////////////

    public function getNodeById($id) {
        $cmd = "
select idNode, id, class, `name`, region, `type`, head, optional from C5Node 
where (id = '{$id}')      
        ";
        $result = $this->query($cmd);
        return (object)$result[0];
    }

    public function getLURelations($lus) {
        $ids = [];
        foreach($lus as $lu) {
            $ids[] = "'{$lu->id}'";
        }
        $list = implode(',', $ids);
        try {
            $command = "
select distinct n1.id lu1, l1.relation, n2.id r1, l2.relation, n3.id p1, l3.relation, n4.id rel, l4.relation, n5.id p2, l5.relation, n6.id r2, l6.relation, n7.id lu2
from C5Node n1
join link l1 on (n1.idnode = l1.idnodesource)
join node n2 on (l1.idnodetarget = n2.idnode)
join link l2 on (n2.idnode = l2.idnodesource)
join node n3 on (l2.idnodetarget = n3.idnode)
join link l3 on (n3.idnode = l3.idnodesource)
join node n4 on (l3.idnodetarget = n4.idnode)
join link l4 on (n4.idnode = l4.idnodetarget)
join node n5 on (l4.idnodesource = n5.idnode)
join link l5 on (n5.idnode = l5.idnodetarget)
join node n6 on (l5.idnodesource = n6.idnode)
join link l6 on (n6.idnode = l6.idnodetarget)
join node n7 on (l6.idnodesource = n7.idnode)
where (n3.name = 'argument1')
and (n5.name = 'argument2')
and (n4.type = 'relation')
and (n1.id in ({$list}))
";
            $result = $this->query($command);
            $relations = [];
            foreach ($result as $record){
                $relations[] = [
                    'lu1' => $this->getNodeById($record['lu1']),
                    'r1' => $this->getNodeById($record['r1']),
                    'p1' => $this->getNodeById($record['p1']),
                    'rel' => $this->getNodeById($record['rel']),
                    'p2' => $this->getNodeById($record['p2']),
                    'r2' => $this->getNodeById($record['r2']),
                    'lu2' => $this->getNodeById($record['lu2']),
                ];
            }
        } catch (Exception $e) {
            $relations = [];
        }
        return $relations;
    }

    public function getEvokedFrame($idLU) {
        try {
            $command = "
select distinct n1.id l, l1.relation, n2.id r, l2.relation, n3.id fe, l3.relation, n4.id frame
from C5Node n1
join link l1 on (n1.idnode = l1.idnodesource)
join node n2 on (l1.idnodetarget = n2.idnode)
join link l2 on (n2.idnode = l2.idnodesource)
join node n3 on (l2.idnodetarget = n3.idnode)
join link l3 on (n3.idnode = l3.idnodesource)
join node n4 on (l3.idnodetarget = n4.idnode)
where (n4.type = 'frame')
and (n1.id = '{$idLU}')
";
            print_r($command);
            print_r($idLU);
            $result = $this->query($command);
            $record = $result[0];
            $node = $this->getNodeById($record['frame']);
        } catch (Exception $e) {
            $node = (object)[];
        }
        return $node;
    }

    public function getPoolsOf($idFeature) {
        try {
            $command = "
select distinct n1.id pool, l1.relation, n2.id feature
from C5Node n1
join link l1 on (n1.idnode = l1.idnodesource)
join node n2 on (l1.idnodetarget = n2.idnode)
where (l1.relation = 'rel_elementof')
and (n2.id = '{$idFeature}')
";
            $result = $this->query($command);
            $nodes = [];
            foreach ($result as $record){
                $nodes[] = $this->getNodeById($record['pool']);
            }
        } catch (Exception $e) {
            $nodes = [];
        }
        return $nodes;
    }

    public function getValuesOf($idPool) {
        //MATCH p=(fe)-[e:ELEMENT_OF]->(f) WHERE f.id='frm_undergoing' RETURN fe LIMIT 50
        try {
            $command = "
select distinct n1.id v, l1.relation, n2.id r, l2.relation, n3.id pool
from C5Node n1
join link l1 on (n1.idnode = l1.idnodesource)
join node n2 on (l1.idnodetarget = n2.idnode)
join link l2 on (n2.idnode = l2.idnodesource)
join node n3 on (l2.idnodetarget = n3.idnode)
where (l1.relation = 'rel_value')
and (n3.id = '{$idPool}')
";

            $result = $this->query($command);
            $values = [];
            foreach ($result as $record){
                $values[] = [
                    'value' => $this->getNodeById($record['v']),
                    'r' => $this->getNodeById($record['r']),
                ];
            }
        } catch (Exception $e) {
            $values = [];
        }
        return $values;
    }

    public function getExtendsFrom($idPool) {
        try {
            $command = "
select distinct n1.id fe1, l1.relation, n2.id r, l2.relation, n3.id fe2
from C5Node n1
join link l1 on (n1.idnode = l1.idnodesource)
join node n2 on (l1.idnodetarget = n2.idnode)
join link l2 on (n2.idnode = l2.idnodesource)
join node n3 on (l2.idnodetarget = n3.idnode)
where (l1.relation = 'rel_extends')
and (n3.id = '{$idPool}')
";

            $result = $this->query($command);
            $values = [];
            foreach ($result as $record){
                $values[] = [
                    'fe1' => $this->getNodeById($record['fe1']),
                    'r' => $this->getNodeById($record['r']),
                ];
            }
        } catch (Exception $e) {
            $values = [];
        }
        return $values;
    }

    public function getExtendsTo($idPool) {
        try {
            $command = "
select distinct n1.id v, l1.relation, n2.id r, l2.relation, n3.id pool, l3.relation, n4.id feature
from C5Node n1
join link l1 on (n1.idnode = l1.idnodesource)
join node n2 on (l1.idnodetarget = n2.idnode)
join link l2 on (n2.idnode = l2.idnodesource)
join node n3 on (l2.idnodetarget = n3.idnode)
join link l3 on (n3.idnode = l3.idnodesource)
join node n4 on (l3.idnodetarget = n4.idnode)
where (l1.relation = 'rel_extends')
and (l3.relation = 'rel_elementof')
and (n1.id = '{$idPool}')
";
            print_r($command);
            $result = $this->query($command);
            $nodes = [];
            foreach ($result as $record){
                $nodes[] = $this->getNodeById($record['feature']);
            }
        } catch (Exception $e) {
            $nodes = [];
        }
        return $nodes;
    }

    public function getConstraints($relays) {
        $ids = [];
        foreach($relays as $relay) {
            $ids[] = "'{$relay}'";
        }
        if (count($ids) == 0) {
            return [];
        }
        $list = implode(',', $ids);
        try {
            $command = "
select distinct n1.id r1, l1.relation, n2.id pool1, l2.relation, n3.id c, l3.relation, n4.id pool2, l4.relation, n5.id r2
from C5Node n1
join link l1 on (n1.idnode = l1.idnodesource)
join node n2 on (l1.idnodetarget = n2.idnode)
join link l2 on (n2.idnode = l2.idnodesource)
join node n3 on (l2.idnodetarget = n3.idnode)
join link l3 on (n3.idnode = l3.idnodetarget)
join node n4 on (l3.idnodesource = n4.idnode)
join link l4 on (n4.idnode = l4.idnodetarget)
join node n5 on (l4.idnodesource = n5.idnode)
where (l2.relation = 'rel_argument1')
and (l3.relation = 'rel_argument2')
and (n1.id IN ({$list}))
and (n5.id IN ({$list}))
";

            $result = $this->query($command);
            $constraints = [];
            foreach ($result as $record){
                $constraints[] = [
                    'r1' => $this->getNodeById($record['r1']),
                    'c' => $this->getNodeById($record['c']),
                    'r2' => $this->getNodeById($record['r2']),
                ];
            }
        } catch (Exception $e) {
            $constraints = [];
        }
        return $constraints;
    }


    public function getCxnByUD($idUD) {
        try {
            $command = "
select distinct n1.id v, l1.relation, n2.id r, l2.relation, n3.id pool, l3.relation, n4.id feature
from C5Node n1
join link l1 on (n1.idnode = l1.idnodesource)
join node n2 on (l1.idnodetarget = n2.idnode)
join link l2 on (n2.idnode = l2.idnodesource)
join node n3 on (l2.idnodetarget = n3.idnode)
join link l3 on (n3.idnode = l3.idnodesource)
join node n4 on (l3.idnodetarget = n4.idnode)
where (l1.relation = 'rel_value')
and (l3.relation = 'rel_elementof')
and (n1.id = '{$idUD}')
";
            print_r($command);
            $result = $this->query($command);
            $nodes = [];
            foreach ($result as $record){
                $nodes[] = $this->getNodeById($record['feature']);
            }
        } catch (Exception $e) {
            $nodes = [];
        }
        return $nodes;
    }


}