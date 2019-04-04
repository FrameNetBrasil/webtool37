<?php
/**
 * Created by PhpStorm.
 * User: ematos
 * Date: 19/07/2018
 * Time: 10:27
 */

namespace C5\ORM\Service;

class GraphService extends BaseService
{
    public function createNode(array $node) {
        $head = $node['head'] ?? 0;
        $optional = $node['optional'] ?? 0;
        $cmd = "
insert into C5Node (id, class, name, region, type, category, head, optional, idEntity) 
values ('{$node['id']}','{$node['class']}','{$node['name']}','{$node['region']}','{$node['type']}', '{$node['category']}', {$head}, {$optional}, {$node['idEntity']})
";
        $this->execute($cmd);
    }

    public function createLink($idSource, $idTarget, $relation) {
        $cmd = "
insert into C5Link (idnodesource, idnodetarget, relation) values  
((select idNode from C5Node where (id = '{$idSource}')),(select idNode from C5Node where id = '{$idTarget}'), '{$relation}');
";
        $this->execute($cmd);

    }

    public function changeRegion($idNode, $region) {
        $cmd = "
update C5Node set region = '{$region}' 
where id = '{$idNode}'
";
        $this->execute($cmd);

    }

    public function createLinkRelay($idSource, $idTarget, $idRelay, $relation) {
        $cmd = "
insert into C5Link (idnodesource, idnodetarget, relation) values  
((select idNode from C5Node where (id = '{$idSource}')),(select idNode from C5Node where id = '{$idRelay}'), '{$relation}'),
((select idNode from C5Node where (id = '{$idRelay}')),(select idNode from C5Node where id = '{$idTarget}'), '{$relation}');
";
        $this->execute($cmd);

    }

    public function createLinkRelayClass($idSource, $idTarget, $idRelay, $relation) {
        $cmd = "
insert into C5Link (idnodesource, idnodetarget, relation) values  
((select idNode from C5Node where (class = '{$idSource}')),(select idNode from C5Node where id = '{$idRelay}'), '{$relation}'),
((select idNode from C5Node where (id = '{$idRelay}')),(select idNode from C5Node where id = '{$idTarget}'), '{$relation}');
";
        $this->execute($cmd);

    }

    public function createLinkConstraint($idPool1, $idPool2, $idConstraint) {
        $cmd = "
insert into C5Link (idnodesource, idnodetarget, relation) values  
((select idNode from C5Node where (id = '{$idPool1}')),(select idNode from C5Node where id = '{$idConstraint}'), 'rel_argument1'),
((select idNode from C5Node where (id = '{$idPool2}')),(select idNode from C5Node where id = '{$idConstraint}'), 'rel_argument2');
";
        $this->execute($cmd);

    }

    public function createUDLink($idHead, $idRel, $idDep) {
        $cmd = "
insert into udlink (idHead, idRel, idDep) values  
((select idNode from C5Node where (id = '{$idHead}')),(select idNode from C5Node where (id = '{$idRel}')),(select idNode from C5Node where (id = '{$idDep}')));
";
        $this->execute($cmd);

    }


    public function getNodeById($id) {
        $cmd = "
select idNode, id, class, `name`, region, `type`, category, head, optional, idEntity 
from C5Node 
where (id = '{$id}')      
        ";
        $result = $this->query($cmd);
        return (object)$result[0];
    }

    public function listNodesByClass($class) {
        $cmd = "
select idNode, id, name 
from C5Node 
where (class = '{$class}')      
        ";
        return $this->query($cmd);
    }

    public function listNodesByType($type) {
        $cmd = "
select idNode, id, name 
from C5Node 
where (type = '{$type}')      
        ";
        return $this->query($cmd);
    }

    public function listLinks() {
        $cmd = "
select s.id as idSource, t.id as idTarget, l.relation, s.optional, s.head
from C5Link l 
join C5Node s on (l.idNodeSource = s.idNode) 
join C5Node t on (l.idNodeTarget = t.idNode) 
        ";
        return $this->query($cmd);
    }

    public function listLinksInput($id) {
        $cmd = "
select s.id as idSource, t.id as idTarget, l.relation, s.optional, s.head
from C5Link l 
join C5Node s on (l.idNodeSource = s.idNode) 
join C5Node t on (l.idNodeTarget = t.idNode) 
where (t.id = '{$id}')      
        ";
        return $this->query($cmd);
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
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
join C5Link l3 on (n3.idnode = l3.idnodesource)
join C5Node n4 on (l3.idnodetarget = n4.idnode)
join C5Link l4 on (n4.idnode = l4.idnodetarget)
join C5Node n5 on (l4.idnodesource = n5.idnode)
join C5Link l5 on (n5.idnode = l5.idnodetarget)
join C5Node n6 on (l5.idnodesource = n6.idnode)
join C5Link l6 on (n6.idnode = l6.idnodetarget)
join C5Node n7 on (l6.idnodesource = n7.idnode)
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
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
join C5Link l3 on (n3.idnode = l3.idnodesource)
join C5Node n4 on (l3.idnodetarget = n4.idnode)
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
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
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
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
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
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
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
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
join C5Link l3 on (n3.idnode = l3.idnodesource)
join C5Node n4 on (l3.idnodetarget = n4.idnode)
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
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
join C5Link l3 on (n3.idnode = l3.idnodetarget)
join C5Node n4 on (l3.idnodesource = n4.idnode)
join C5Link l4 on (n4.idnode = l4.idnodetarget)
join C5Node n5 on (l4.idnodesource = n5.idnode)
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

    public function getConstraintsByRegion($region) {
        try {
            $command = "
select distinct n1.id r1, l1.relation, n2.id pool1, l2.relation, n3.id c, l3.relation, n4.id pool2, l4.relation, n5.id r2
from C5Node n1
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
join C5Link l3 on (n3.idnode = l3.idnodetarget)
join C5Node n4 on (l3.idnodesource = n4.idnode)
join C5Link l4 on (n4.idnode = l4.idnodetarget)
join C5Node n5 on (l4.idnodesource = n5.idnode)
where (l2.relation = 'rel_argument1')
and (l3.relation = 'rel_argument2')
and (n3.type = 'constraint')
and (n3.region like '%{$region}')
";

            $result = $this->query($command);
            $constraints = [];
            foreach ($result as $record){
                $constraints[] = [
                    'r1' => $record['r1'],//$this->getNodeById($record['r1']),
                    'c' => $this->getNodeById($record['c']),
                    'r2' => $record['r2'],//$this->getNodeById($record['r2']),
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
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
join C5Link l3 on (n3.idnode = l3.idnodesource)
join C5Node n4 on (l3.idnodetarget = n4.idnode)
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

    public function getCxnByPOS($idPOS) {
        try {
            $command = "
select distinct n1.id v, l1.relation, n2.id r, l2.relation, n3.id pool, l3.relation, n4.id feature
from C5Node n1
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
join C5Link l3 on (n3.idnode = l3.idnodesource)
join C5Node n4 on (l3.idnodetarget = n4.idnode)
where (l1.relation = 'rel_value')
and (l3.relation = 'rel_elementof')
and (n1.id = '{$idPOS}')
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

    public function getFeatureFromValue($idNodeTypeValue) {
        try {
            $command = "
select distinct n1.id value, l1.relation, n2.id relay, l2.relation, n3.id pool, l3.relation, n4.id feature
from C5Node n1
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
join C5Link l3 on (n3.idnode = l3.idnodesource)
join C5Node n4 on (l3.idnodetarget = n4.idnode)
where (n1.id = '{$idNodeTypeValue}')
and (l1.relation = 'rel_value')
and (n4.type = 'cxn')
-- and (n2.head = 1)
";
            $result = $this->query($command);
            $features = [];
            foreach ($result as $record){
                $features[] = $record['feature'];
            }
        } catch (Exception $e) {
            $features = [];
        }
        return $features;
    }

    public function getFeatureFromValueClass($classNodeTypeValue) {
        try {
            $command = "
select distinct n1.id value, l1.relation, n2.id relay, l2.relation, n3.id pool, l3.relation, n4.id feature
from C5Node n1
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
join C5Link l3 on (n3.idnode = l3.idnodesource)
join C5Node n4 on (l3.idnodetarget = n4.idnode)
where (n1.class = '{$classNodeTypeValue}')
and (l1.relation = 'rel_value')
and ((n4.type = 'cxn') or (n4.type = 'inhibitory'))
-- and (n2.head = 1)
";
            $result = $this->query($command);
            $features = [];
            foreach ($result as $record){
                $features[$record['feature']] = $record['feature'];
            }
        } catch (Exception $e) {
            $features = [];
        }
        return $features;
    }

    public function getFeatureStructure($idFeatureNode) {
        try {
            $command = "
select distinct n1.id v1, l1.relation, n2.id r1, l2.relation, n3.id p1, l3.relation, n4.id f1
from C5Node n1
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
join C5Link l3 on (n3.idnode = l3.idnodesource)
join C5Node n4 on (l3.idnodetarget = n4.idnode)
join C5Link l4 on (n4.idnode = l4.idnodetarget)
where (n4.id = '{$idFeatureNode}')
";
            $result = $this->query($command);
            $relations = [];
            foreach ($result as $record){
                $relations[] = [
                    'v' => $this->getNodeById($record['v1']),
                    'r' => $this->getNodeById($record['r1']),
                    'p' => $this->getNodeById($record['p1']),
                    'f' => $this->getNodeById($record['f1']),
                ];
            }
        } catch (Exception $e) {
            $relations = [];
        }
        return $relations;
    }


    public function ListParentByChild($region, $typeParent, $children) {
        try {
            $ids = [];
            foreach($children as $child) {
                $ids[] = "'{$child}'";
            }
            if (count($ids) == 0) {
                return [];
            }
            $list = implode(',', $ids);
            if ($typeParent == 'root') {
                $where = '';
                $wRegion = "and (n4.class = 'root') ";
            } else {
                $where = "and (n2.id <> n6.id) ";
                if ($region != 'pos') {
                    $wRegion = "";//"and (n4.class = '{$region}') ";
                } else {
                    $wRegion = "and (n4.region = '{$region}') ";
                }
            }


            $command = "
select distinct n1.id v1, l1.relation, n2.id r1, l2.relation, n3.id p1, l3.relation, n4.id parent, l4.relation, n5.id p2, l5.relation, n6.id r2, l6.relation, n7.id v2
from C5Node n1
join C5Link l1 on (n1.idnode = l1.idnodesource)
join C5Node n2 on (l1.idnodetarget = n2.idnode)
join C5Link l2 on (n2.idnode = l2.idnodesource)
join C5Node n3 on (l2.idnodetarget = n3.idnode)
join C5Link l3 on (n3.idnode = l3.idnodesource)
join C5Node n4 on (l3.idnodetarget = n4.idnode)
join C5Link l4 on (n4.idnode = l4.idnodetarget)
join C5Node n5 on (l4.idnodesource = n5.idnode)
join C5Link l5 on (n5.idnode = l5.idnodetarget)
join C5Node n6 on (l5.idnodesource = n6.idnode)
join C5Link l6 on (n6.idnode = l6.idnodetarget)
join C5Node n7 on (l6.idnodesource = n7.idnode)
where (n1.id in ({$list}))
and (n7.id in ({$list}))
{$where}
{$wRegion}
and (n4.type = '{$typeParent}')
and (n2.head = 1) and (n6.head = 0)
";
            $result = $this->query($command);
            $relations = [];
            foreach ($result as $record){
                $relations[] = [
                    'v1' => $this->getNodeById($record['v1']),
                    'r1' => $this->getNodeById($record['r1']),
                    'p1' => $this->getNodeById($record['p1']),
                    'parent' => $this->getNodeById($record['parent']),
                    'p2' => $this->getNodeById($record['p2']),
                    'r2' => $this->getNodeById($record['r2']),
                    'v2' => $this->getNodeById($record['v2']),
                ];
            }
        } catch (Exception $e) {
            $relations = [];
        }
        return $relations;
    }

    public function countNodes() {
        $cmd = "
select count(*) c, name from C5Node
        ";
        $result = $this->query($cmd);
        return $result[0]['c'];

    }

    public function countLinks() {
        $cmd = "
select count(*) c from C5Link
        ";
        $result = $this->query($cmd);
        return $result[0]['c'];

    }

}