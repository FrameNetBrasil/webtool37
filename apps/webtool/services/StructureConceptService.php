<?php



class StructureConceptService extends MService
{

    public function listDomains($data = '', $idLanguage = '')
    {
        $domain = new fnbr\models\Domain();
        $domains = $domain->listAll()->asQuery()->getResult();
        $result = array();
        foreach ($domains as $row) {
            $node = array();
            $node['id'] = 'd' . $row['idDomain'];
            $node['text'] = $row['name'];
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listConceptsRoot($data = '', $idLanguage = '')
    {
        $concept = new fnbr\models\Concept();
        $filter = (object) ['type' => $data->type, 'idLanguage' => $idLanguage];
        $types = $concept->listRoot($filter)->asQuery()->getResult();
        $result = array();
        foreach ($types as $row) {
            $node = array();
            $node['id'] = 'c' . $row['idConcept'];
            $node['text'] = $row['name'];
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listConceptsChildren($idSuperType, $idLanguage = '')
    {
        $concept = new fnbr\models\Concept();
        $filter = (object) ['type' => $data->type, 'idLanguage' => $idLanguage];
        $types = $concept->listChildren($idSuperType, $filter)->asQuery()->getResult();
        $result = array();
        foreach ($types as $row) {
            $node = array();
            $node['id'] = 'c' . $row['idConcept'];
            $node['text'] = $row['name'];
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
