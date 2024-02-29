<?php



class StructureConceptService extends MService
{

    public function listConceptsRoot($data = '', $idLanguage = '')
    {
        $typeInstance = [
            106 => 'CPT',
            107 => 'SEM',
            108 => 'CXN',
            109 => 'STR',
            110 => 'INF',
        ];
        $concept = new fnbr\models\Concept();
        $filter = (object) ['type' => $data->type, 'idLanguage' => $idLanguage];
        $types = $concept->listRoot($filter)->asQuery()->getResult();
        $result = array();
        foreach ($types as $row) {
            $node = array();
            $node['id'] = 'c' . $row['idConcept'];
            $node['text'] = $row['name'] . ' [' . $typeInstance[$row['idTypeInstance']] . ']';
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listConceptsTypeRoot($idTypeInstance = '', $idLanguage = '')
    {
        $typeInstance = [
            106 => 'CPT',
            107 => 'SEM',
            108 => 'CXN',
            109 => 'STR',
            110 => 'INF',
        ];
        $concept = new fnbr\models\Concept();
        mdump('===='. $idTypeInstance);
        $filter = (object) ['idTypeInstance' => $idTypeInstance, 'idLanguage' => $idLanguage];
        $types = $concept->listRoot($filter)->asQuery()->getResult();
        $result = array();
        foreach ($types as $row) {
            $node = array();
            $node['id'] = 'c' . $row['idConcept'];
            $node['text'] = $row['name'] . ' [' . $typeInstance[$row['idTypeInstance']] . ']';
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listConceptsChildren($idSuperType, $idLanguage = '')
    {
        $typeInstance = [
            106 => 'CPT',
            107 => 'SEM',
            108 => 'CXN',
            109 => 'STR',
            110 => 'INF',
        ];
        $concept = new fnbr\models\Concept();
        $filter = (object) ['type' => $data->type, 'idLanguage' => $idLanguage];
        $types = $concept->listChildren($idSuperType, $filter)->asQuery()->getResult();
        $result = array();
        foreach ($types as $row) {
            $node = array();
            $node['id'] = 'c' . $row['idConcept'];
            $node['text'] = $row['name'] . ' [' . $typeInstance[$row['idTypeInstance']] . ']';
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        $types = $concept->listElements($idSuperType, $filter)->asQuery()->getResult();
        foreach ($types as $row) {
            $node = array();
            $node['id'] = 'e' . $row['idConcept'] . '_' . $idSuperType;
            $node['text'] = $row['name'];
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $node['iconCls'] = "icon-blank  fa fa-life-ring fa16px";
            $result[] = $node;
        }
        return $result;
    }

    public function listConceptsParent($idSubType, $idLanguage = '')
    {
        $concept = new fnbr\models\Concept();
        $filter = (object) ['idLanguage' => $idLanguage];
        $types = $concept->listParent($idSubType, $filter)->asQuery()->getResult();
        return $types;
    }

    public function listConceptsAssociatedTo($idSubType, $idLanguage = '')
    {
        $concept = new fnbr\models\Concept();
        $filter = (object) ['idLanguage' => $idLanguage];
        $types = $concept->listAssociatedTo($idSubType, $filter)->asQuery()->getResult();
        return $types;
    }

    public function listEntityConcepts($id)
    {
        $concept = new fnbr\models\Concept();
        $types = $concept->listTypesByEntity($id)->asQuery()->getResult();
        $result = array();
        foreach ($types as $row) {
            $node = array();
            $node['idConcept'] = $row['idConcept'];
            $node['idEntity'] = $row['idEntity'];
            $node['name'] = $row['domainName'] . '.' . $row['name'];
            $result[] = $node;
        }
        return $result;
    }

    public function listConceptsByName($name, $idLanguage)
    {
        $typeInstance = [
            106 => 'CPT',
            107 => 'SEM',
            108 => 'CXN',
            109 => 'STR',
            110 => 'INF',
        ];

        $concept = new fnbr\models\Concept();
        $types = $concept->listByName($name, $idLanguage)->asQuery()->getResult();
        $result = array();
        foreach ($types as $row) {
            $node = array();
            $node['id'] = 'c' . $row['idConcept'];
            $node['text'] = $row['name'] . ' [' . $typeInstance[$row['idTypeInstance']] . ']';
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function addEntityConcept($idEntity, $idConcept) {
        $concept = new fnbr\models\Concept($idConcept);
        $concept->addEntity($idEntity);
    }
    
    public function delEntityConcept($idEntity, $toRemove) {
        $concept = new fnbr\models\Concept();
        $idConceptEntity = [];
        foreach($toRemove as $st) {
            $idConceptEntity[] = $st->idEntity;
        }
        $concept->delConceptFromEntity($idEntity, $idConceptEntity);
    }
    
}
