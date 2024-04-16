<?php

Manager::import("fnbr\models\*");

class GrapherQualiaService extends MService
{

    /*
     *  LUs
     */

    public function listLUs($data, $idLanguage = '')
    {
        $lu = new \fnbr\models\LU();
        mdump('==' . $idLanguage);
        $filter = (object)['name' => $data->lu ?? '-', 'idLanguage' => $idLanguage];
        $lus = $lu->listByFilter($filter)->asQuery()->getResult(\FETCH_ASSOC);
        $result = array();
        foreach ($lus as $row) {
            //if (strpos($row['name'], '#') === false) {
            $node = array();
            $node['id'] = 'l' . $row['idEntity'];
            $node['text'] = $row['name'] . ' [' . $row['frameName'] . ']';
            $node['state'] = 'open';
            $node['iconCls'] = 'icon-blank fa fa-square fa16px entity_lu';
            $node['entry'] = $row['entry'];
            $result[] = $node;
            //}
        }
        return $result;
    }

    /*
     * Qualia Relations
     */

    public function getRelations($idEntity, $chosen, $level = 1)
    {
        $relations = $this->getQualiaRelationsById($idEntity);
        $parents = $relations;
        while (count($parents) > 0) {
            $next = [];
            foreach ($parents as $parent) {
                if ($parent['type'] == 'rel_qualia_formal') {
                    $idEntity = $parent['target']->id;
                    $newRelations = $this->getQualiaFormalRelationsById($idEntity);
                    foreach ($newRelations as $newRelation) {
                        $next[] = $newRelation;
                    }
                }
            }
            if (count($next) > 0) {
                foreach ($next as $n) {
                    $relations[] = $n;
                }
            }
            $parents = $next;
        }
        return json_encode($relations);
    }

    public function getQualiaRelationsById($idEntity)
    {
        mdump('==  getEntityRelationsById ' . $idEntity);
        $entity = new \fnbr\models\Entity($idEntity);
        $relations = array_merge($this->getQualiaDirectRelations($idEntity), $this->getQualiaInverseRelations($idEntity));
        if (count($relations) == 0) {
            $node = (object)[
                'id' => $idEntity,
                'type' => $entity->getTypeNode(),
                'name' => $entity->getName()
            ];
            $relations[] = ['source' => $node, 'type' => 'rel_none', 'target' => $node];
        }
        mdump('======================================');
        return $relations;
    }

    public function getQualiaFormalRelationsById($idEntity)
    {
        $entity = new \fnbr\models\Entity($idEntity);
        $relations = [];
        $node0 = (object)[
            'id' => $idEntity,
            'type' => $entity->getTypeNode(),
            'name' => $entity->getName()
        ];
        $filter = [
            'rel_qualia_formal'
        ];
        $directRelations = $entity->listQualiaDirectRelations($filter);
        foreach ($directRelations as $entry => $row) {
            foreach ($row as $r) {
                $node1 = (object)[
                    'id' => $r['idEntity'],
                    'type' => $r['type'],
                    'name' => $r['name'] . '[' . $r['frame'] . ']'
                ];
                $relations[] = ['source' => $node0, 'type' => $entry, 'target' => $node1];
            }
        }
        mdump('======================================');
        return $relations;
    }

    public function getQualiaDirectRelations($idEntity)
    {
        mdump('======================================');
        mdump('getQualiaDirectRelations - ' . $idEntity);
        mdump('======================================');
        $entity = new \fnbr\models\Entity($idEntity);
        $relations = [];
        $node0 = (object)[
            'id' => $idEntity,
            'type' => $entity->getTypeNode(),
            'name' => $entity->getName()
        ];
        $filter = [
            'rel_qualia_formal',
            'rel_qualia_constitutive',
            'rel_qualia_agentive',
            'rel_qualia_telic',
        ];
        $directRelations = $entity->listQualiaDirectRelations($filter);
        foreach ($directRelations as $entry => $row) {
            foreach ($row as $r) {
                $node1 = (object)[
                    'id' => $r['idEntity'],
                    'type' => $r['type'],
                    'name' => $r['name'] . '[' . $r['frame'] . ']'
                ];
                $relations[] = ['source' => $node0, 'type' => $entry, 'target' => $node1];
            }
        }
        mdump($relations);
        mdump('======================================');
        return $relations;
    }

    public function getQualiaInverseRelations($idEntity)
    {
        mdump('======================================');
        mdump('getQualiaInverseRelations - ' . $idEntity);
        mdump('======================================');

        $entity = new \fnbr\models\Entity($idEntity);
        $relations = [];
        $node1 = (object)[
            'id' => $idEntity,
            'type' => $entity->getTypeNode(),
            'name' => $entity->getName()
        ];
        $filter = [
            'rel_qualia_formal',
            'rel_qualia_constitutive',
            'rel_qualia_agentive',
            'rel_qualia_telic',
        ];
        $inverseRelations = $entity->listQualiaInverseRelations($filter);
        foreach ($inverseRelations as $entry => $row) {
            foreach ($row as $r) {
                $node0 = (object)[
                    'id' => $r['idEntity'],
                    'type' => $r['type'],
                    'name' => $r['name'] . '[' . $r['frame'] . ']'
                ];
                $relations[] = ['source' => $node0, 'type' => $entry, 'target' => $node1];
            }
        }
        mdump($relations);
        mdump('======================================');
        return $relations;
    }

}
