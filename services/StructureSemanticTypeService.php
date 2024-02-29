<?php


use fnbr\models\Base;
use fnbr\models\EntityRelation;
use fnbr\models\Frame;
use fnbr\models\RelationType;
use fnbr\models\SemanticType;

class StructureSemanticTypeService extends MService
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
    
    public function listSemanticTypesRoot($data = '', $idDomain = '', $idLanguage = '')
    {
        $semanticType = new fnbr\models\SemanticType();
        $filter = (object) ['type' => $data->type, 'idDomain' => $idDomain, 'idLanguage' => $idLanguage];
        $types = $semanticType->listRoot($filter)->asQuery()->getResult();
        $result = array();
        foreach ($types as $row) {
            $node = array();
            $node['id'] = 't' . $row['idSemanticType'];
            $node['text'] = $row['name'];
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listSemanticTypesChildren($idSuperType, $idLanguage = '')
    {
        $semanticType = new fnbr\models\SemanticType();
        $filter = (object) ['type' => $data->type, 'idLanguage' => $idLanguage];
        $types = $semanticType->listChildren($idSuperType, $filter)->asQuery()->getResult();
        $result = array();
        foreach ($types as $row) {
            $node = array();
            $node['id'] = 't' . $row['idSemanticType'];
            $node['text'] = $row['name'];
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listEntitySemanticTypes($id)
    {
        $semanticType = new fnbr\models\SemanticType();
        $types = $semanticType->listTypesByEntity($id)->asQuery()->getResult();
        $result = array();
        foreach ($types as $row) {
            $node = array();
            $node['idSemanticType'] = $row['idSemanticType'];
            $node['idEntity'] = $row['idEntity'];
            $node['name'] = $row['domainName'] . '.' . $row['name'];
            $result[] = $node;
        }
        return $result;
    }

    public function addEntitySemanticType($idEntity, $idSemanticType) {
        $semanticType = new fnbr\models\SemanticType($idSemanticType);
        $semanticType->addEntity($idEntity);
    }
    
    public function delEntitySemanticType($idEntity, $toRemove) {
        $semanticType = new fnbr\models\SemanticType();
        $idSemanticTypeEntity = [];
        foreach($toRemove as $st) {
            $idSemanticTypeEntity[] = $st->idEntity;
        }
        $semanticType->delSemanticTypeFromEntity($idEntity, $idSemanticTypeEntity);
    }

    public function updateFrameCluster($idFrame, $list) {
        $frame = Frame::create($idFrame);
        $rt = new RelationType();
        $c = $rt->getCriteria()->select('idRelationType')->where("entry = 'rel_framal_cluster'");
        $er = new EntityRelation();
        $transaction = $er->beginTransaction();
        $criteria = $er->getDeleteCriteria();
        $criteria->where("idEntity1 = {$frame->getIdEntity()}");
        $criteria->where("idRelationType", "=", $c);
        $criteria->delete();
        foreach($list as $cluster) {
            $st = SemanticType::create($cluster->idSemanticType);
            Base::createEntityRelation($frame->getIdEntity(), 'rel_framal_cluster', $st->getIdEntity());
        }
        $transaction->commit();
    }

    public function updateFrameType($idFrame, $list) {
        $frame = Frame::create($idFrame);
        $rt = new RelationType();
        $c = $rt->getCriteria()->select('idRelationType')->where("entry = 'rel_framal_type'");
        $er = new EntityRelation();
        $transaction = $er->beginTransaction();
        $criteria = $er->getDeleteCriteria();
        $criteria->where("idEntity1 = {$frame->getIdEntity()}");
        $criteria->where("idRelationType", "=", $c);
        $criteria->delete();
        foreach($list as $cluster) {
            $st = SemanticType::create($cluster->idSemanticType);
            Base::createEntityRelation($frame->getIdEntity(), 'rel_framal_type', $st->getIdEntity());
        }
        $transaction->commit();
    }

    public function updateFrameDomain($idFrame, $list) {
        $frame = Frame::create($idFrame);
        $rt = new RelationType();
        $c = $rt->getCriteria()->select('idRelationType')->where("entry = 'rel_framal_domain'");
        $er = new EntityRelation();
        $transaction = $er->beginTransaction();
        $criteria = $er->getDeleteCriteria();
        $criteria->where("idEntity1 = {$frame->getIdEntity()}");
        $criteria->where("idRelationType", "=", $c);
        $criteria->delete();
        foreach($list as $cluster) {
            $st = SemanticType::create($cluster->idSemanticType);
            Base::createEntityRelation($frame->getIdEntity(), 'rel_framal_domain', $st->getIdEntity());
        }
        $transaction->commit();
    }

}
