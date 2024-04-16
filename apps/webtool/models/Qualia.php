<?php
namespace fnbr\models;

class Qualia extends map\QualiaMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'info' => array('notnull'),
                'idEntity' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription()
    {
        return $this->getInfo();
    }

    public function getName()
    {
        $criteria = $this->getCriteria()->select('entries.name as name');
        $criteria->where("idQualia = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->getResult()[0]['name'];
    }

    public function getTypeFromRelation($relation) {
        $type = [
            'rel_qualia_formal' => 'qla_formal',
            'rel_qualia_agentive' => 'qla_agentive',
            'rel_qualia_telic' => 'qla_telic',
            'rel_qualia_constitutive' => 'qla_constitutive'
        ];
        $relationEntry = $relation->getRelationtype()->getEntry();
        return $type[$relationEntry];
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('idQualia, entry, entries.name as name, typeinstance.entry qualiaType')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        if ($filter->idTypeInstance) {
            $criteria->where("idTypeInstance = {$filter->idTypeInstance}");
        }
        if ($filter->idFrame) {
            $criteria->where("idFrame = {$filter->idFrame}");
        }
        if ($filter->idFrameElement1) {
            $criteria->where("idFrameElement1 = {$filter->idFrameElement1}");
        }
        if ($filter->idFrameElement2) {
            $criteria->where("idFrameElement2 = {$filter->idFrameElement2}");
        }
        if ($filter->relation) {
            $criteria->where("entries.name LIKE '{$filter->relation}%'");
        }
        return $criteria;
    }

    public function listByFrame($idFrame, $idLanguage = '1')
    {
//        $cmd = <<<HERE
//        SELECT q.idQualia, concat(t.entry, ' [',eq.name,']') name
//        FROM Qualia q
//        JOIN Entry eq on (q.entry = eq.entry)
//        JOIN TypeInstance t on (q.idTypeInstance = t.idTypeInstance)
//        JOIN View_Relation r on (r.idEntity1 = q.idEntity)
//        JOIN Frame f on (r.idEntity2 = f.idEntity)
//        WHERE (r.relationType = 'rel_qualia_frame')
//          AND (f.idFrame = {$idFrame})
//          AND (eq.idLanguage = {$idLanguage})
//        ORDER BY t.entry
//
//HERE;

        $cmd = <<<HERE
        SELECT q.idQualia, concat(q.info, ' [',t.entry,']') name
        FROM Qualia q
        JOIN Entry eq on (q.entry = eq.entry)
        JOIN TypeInstance t on (q.idTypeInstance = t.idTypeInstance)
        JOIN Frame f on (q.idFrame = f.idFrame)
        WHERE (f.idFrame = {$idFrame})
          AND (eq.idLanguage = {$idLanguage})
        ORDER BY t.entry

HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->chunkResult('idQualia', 'name');
        return $result;
    }

    public function listFEs($idLanguage = '1')
    {
        $idQualia = $this->getId();
//        $cmd = <<<HERE
//        SELECT r.idEntityRelation, concat('lu1: ',e.name) name
//        FROM Qualia q
//        JOIN View_Relation r on (r.idEntity1 = q.idEntity)
//        JOIN FrameElement fe on (r.idEntity2 = fe.idEntity)
//        JOIN Entry e on (fe.entry = e.entry)
//        WHERE (r.relationType = 'rel_qualia_lu1_fe')
//          AND (q.idQualia = {$idQualia})
//          AND (e.idLanguage = {$idLanguage})
//        UNION
//        SELECT r.idEntityRelation, concat('lu2: ',e.name) name
//        FROM Qualia q
//        JOIN View_Relation r on (r.idEntity1 = q.idEntity)
//        JOIN FrameElement fe on (r.idEntity2 = fe.idEntity)
//        JOIN Entry e on (fe.entry = e.entry)
//        WHERE (r.relationType = 'rel_qualia_lu2_fe')
//          AND (q.idQualia = {$idQualia})
//          AND (e.idLanguage = {$idLanguage})
//
//HERE;
        $cmd = <<<HERE
        SELECT q.idFrameElement1 idFrameElement, concat('lu1: ',e.name) name
        FROM Qualia q
        JOIN FrameElement fe on (q.idFrameElement1 = fe.idFrameElement)
        JOIN Entry e on (fe.entry = e.entry)
        WHERE (q.idQualia = {$idQualia})
          AND (e.idLanguage = {$idLanguage})
        UNION
        SELECT q.idFrameElement2 idFrameElement, concat('lu2: ',e.name) name
        FROM Qualia q
        JOIN FrameElement fe on (q.idFrameElement2 = fe.idFrameElement)
        JOIN Entry e on (fe.entry = e.entry)
        WHERE (q.idQualia = {$idQualia})
          AND (e.idLanguage = {$idLanguage})

HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->chunkResult('idFrameElement', 'name');
        return $result;
    }

    public function listRelationForLookup($qualiaType = '', $idLanguage = '1')
    {
        $criteria = $this->getCriteria()->select('idQualia, entry, entries.name as name')->orderBy('entries.name');
        $criteria->where("typeinstance.entry = '{$qualiaType}'");
        Base::entryLanguage($criteria);
        return $criteria->asQuery();
    }

    public function listForLookup($type = '', $idLanguage = '1')
    {
        $whereType = ($type == '') ? '' : "AND (t.entry = '{$type}')";
        $name= ($type == '') ? "concat(substr(t.entry,5,15),': ', eq.name, ' [',e.name,']') name" : "concat(eq.name, ' [',e.name,']') name";
//        $cmd = <<<HERE
//        SELECT q.idQualia, {$name}
//        FROM Qualia q
//        JOIN Entry eq on (q.entry = eq.entry)
//        JOIN TypeInstance t on (q.idTypeInstance = t.idTypeInstance)
//        JOIN Frame f on (q.idFrame = f.idFrame)
//        JOIN Entry e on (f.entry = e.entry)
//        WHERE {$whereType}
//          AND (e.idLanguage = {$idLanguage})
//          AND (eq.idLanguage = {$idLanguage})
//        ORDER BY t.entry, q.info
//
//HERE;

        $cmd = <<<HERE
        SELECT q.idQualia, {$name}
        FROM Qualia q
        JOIN Entry eq on (q.entry = eq.entry)
        JOIN TypeInstance t on (q.idTypeInstance = t.idTypeInstance)
        JOIN Frame f on (q.idFrame = f.idFrame)
        JOIN Entry e on (f.entry = e.entry)
        WHERE (e.idLanguage = {$idLanguage})
          AND (eq.idLanguage = {$idLanguage})
          {$whereType}
        ORDER BY t.entry, q.info

HERE;
        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function listForGrid($data, $idLanguage = '1')
    {
        $whereType = ($data->idQualiaType == '') ? '' : "AND (t.idTypeInstance  = {$data->idQualiaType})";
        $whereFrame = ($data->frame == '') ? '' : "AND (upper(ef.name) like upper('{$data->frame}%'))";

//        $cmd = <<<HERE
//select q.idQualia, q.info, q.qualiaEntry, q.qualiaType, f.frame, fe1.fe fe1, fe1.typeEntry fe1Type,fe2.fe fe2, fe2.typeEntry fe2Type
//from (
//  select q.idQualia, eq.name info, t.entry qualiaEntry, et.name qualiaType, t.idTypeInstance idQualiaType, q.idEntity idEntityQualia
//  FROM Qualia q
//  JOIN Entry eq ON (q.entry = eq.entry)
//  JOIN TypeInstance t ON (q.idTypeInstance = t.idTypeInstance)
//  JOIN Entry et ON (t.entry = et.entry)
//  WHERE (eq.idLanguage = {$idLanguage})
//  AND (et.idLanguage = {$idLanguage})
//
//) q,
//(
//select e.name frame, r.identity1 idEntityQualia
//from view_relation r
//join frame f on (r.idEntity2 = f.idEntity)
//join Entry e on (f.entry = e.entry)
//where (r.relationType = 'rel_qualia_frame')
//and (e.idLanguage = {$idLanguage})
//) f,
//(
//select e.name fe, ti.entry typeEntry, er1.identity1 idEntityQualia
//from entityrelation er1
//join relationtype rt on (er1.idRelationType = rt.idRelationType)
//join frameelement fe on (er1.idEntity2 = fe.idEntity)
//join entityrelation er2 on (er1.idEntity2 = er2.idEntity1)
//join typeinstance ti on (er2.idEntity2 = ti.idEntity)
//join Entry e on (fe.entry = e.entry)
//where (rt.entry = 'rel_qualia_lu1_fe')
//and (e.idLanguage = {$idLanguage})
//) fe1,
//(
//select e.name fe, ti.entry typeEntry, er1.identity1 idEntityQualia
//from entityrelation er1
//join relationtype rt on (er1.idRelationType = rt.idRelationType)
//join frameelement fe on (er1.idEntity2 = fe.idEntity)
//join entityrelation er2 on (er1.idEntity2 = er2.idEntity1)
//join typeinstance ti on (er2.idEntity2 = ti.idEntity)
//join Entry e on (fe.entry = e.entry)
//where (rt.entry = 'rel_qualia_lu2_fe')
//and (e.idLanguage = {$idLanguage})
//) fe2
//where (q.idEntityQualia = f.idEntityQualia)
//and (q.idEntityQualia = fe1.idEntityQualia)
//and (q.idEntityQualia = fe2.idEntityQualia)
//{$whereType} {$whereFrame}
//        ORDER BY 3,2,4
//
//HERE;

        $cmd = <<<HERE
select q.idQualia, eq.name info, t.entry qualiaEntry, et.name qualiaType, t.idTypeInstance idQualiaType, ef.name frame, efe1.name fe1, fe1.coreType fe1Type,efe2.name fe2, fe2.coreType fe2Type
  FROM Qualia q
  JOIN Entry eq ON (q.entry = eq.entry)
  JOIN TypeInstance t ON (q.idTypeInstance = t.idTypeInstance)
  JOIN Entry et ON (t.entry = et.entry)
join frame f on (q.idFrame = f.idFrame)
join Entry ef on (f.entry = ef.entry)
join frameelement fe1 on (q.idFrameElement1 = fe1.idFrameElement)
join Entry efe1 on (fe1.entry = efe1.entry)
join frameelement fe2 on (q.idFrameElement2 = fe2.idFrameElement)
join Entry efe2 on (fe2.entry = efe2.entry)
  WHERE (eq.idLanguage = {$idLanguage})
  AND (et.idLanguage = {$idLanguage})
  AND (ef.idLanguage = {$idLanguage})
  AND (efe1.idLanguage = {$idLanguage})
  AND (efe2.idLanguage = {$idLanguage})
{$whereType} {$whereFrame}
        ORDER BY 3,2,4

HERE;

        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function listRelationForGrid($data, $idLanguage = '1')
    {
        $whereType = ($data->idQualiaType == '') ? '' : "AND (t.idTypeInstance = {$data->idQualiaType})";
        $whereLU1 = ($data->lu1 == '') ? '' : "AND (upper(lu1.name) like upper('{$data->lu1}%'))";
        $whereLU2 = ($data->lu2 == '') ? '' : "AND (upper(lu2.name) like upper('{$data->lu2}%'))";
        $whereRelation = ($data->relation == '') ? '' : "AND (upper(q.info) like upper('{$data->relation}%'))";
        $cmd = <<<HERE
select r.idEntityRelation, substr(r.relationType, 12,20) qualiaType, lu1.name lu1, eq.name relation, lu2.name lu2
from View_Relation r
JOIN View_LU lu1 on (r.idEntity1 = lu1.idEntity)
JOIN View_LU lu2 on (r.idEntity2 = lu2.idEntity)
JOIN TypeInstance t on (t.entry = concat('qla_',substr(r.relationType, 12,20)))
LEFT JOIN Qualia q on (r.idEntity3 = q.idEntity)
LEFT JOIN Entry eq on (q.entry = eq.entry)
where (r.relationGroup = 'rgp_qualia')
AND (eq.idLanguage = {$idLanguage})
AND (lu1.idLanguage = {$idLanguage})
AND (lu2.idLanguage = {$idLanguage}) {$whereType} {$whereLU1} {$whereLU2} {$whereRelation}
order by 2,3,4

HERE;
        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function listLUQualia($idLU)
    {
        $constraint = new ViewConstraint();
        $lu = new LU($idLU);
        $constraints = [];
        $qualiaConstraints = $constraint->listLUQualiaConstraints($lu->getIdEntity());
        foreach ($qualiaConstraints as $qualia) {
            $constraints[] = $qualia;
        }
        return $constraints;
    }

    public function save($data)
    {
        $transaction = $this->beginTransaction();
        try {
            $alias = $data->entry;
            $entity = new Entity();
            $entity->setAlias($alias);
            $entity->setType('QR');  // Qualia Relation
            $entity->save();
            $this->setIdEntity($entity->getId());
            Base::entityTimelineSave($this->getIdEntity());
            $entry = new Entry();
            $entry->newEntry($data->entry);
            $entry->setIdEntity($entity->getId());
            $this->setIdTypeInstance($data->idTypeInstance);
            $this->setInfo($data->entry);
            $this->setEntry($data->entry);
            parent::save();
            Timeline::addTimeline("qualia",$this->getId(),"S");
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function saveData($data)
    {
        $transaction = $this->beginTransaction();
        try {
            $typeInstance = new TypeInstance();
            $typeInstance->getByEntry($data->type);
            $this->setIdTypeInstance($typeInstance->getIdTypeInstance());
            $strEntry = 'qla_' . str_replace('qla_','', $data->info) . '_' . substr(uniqid(),0,3);
            $entity = new Entity();
            $entity->setAlias($strEntry);
            $entity->setType('QR');  // Qualia Structure
            $entity->save();
            $this->setIdEntity($entity->getId());
            $entry = new Entry();
            $entry->newEntry($strEntry,$entity->getId());
            $this->setIdTypeInstance($typeInstance->getIdTypeInstance());
            $this->setInfo($data->info);
            $this->setEntry($strEntry);
            $this->setIdFrame($data->idFrame);
            $this->setIdFrameElement1($data->idFE1);
            $this->setIdFrameElement2($data->idFE2);
            parent::save();
            Timeline::addTimeline("qualia",$this->getId(),"S");
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function saveRelation($data)
    {
        $transaction = $this->beginTransaction();
        try {
            $lu1 = new LU($data->idLU1);
            $lu2 = new LU($data->idLU2);
            $this->getById($data->idQualia);
            $relationType = [
                'qla_formal' => 'rel_qualia_formal',
                'qla_agentive' => 'rel_qualia_agentive',
                'qla_telic' => 'rel_qualia_telic',
                'qla_constitutive' => 'rel_qualia_constitutive',
            ];
            Base::createEntityRelation($lu1->getIdEntity(), $relationType[$data->type], $lu2->getIdEntity(), $this->getIdEntity());
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete()
    {
        $transaction = $this->beginTransaction();
        try {
            $idEntity = $this->getIdEntity();
 //           Base::entityTimelineDelete($idEntity);
            Base::deleteAllEntityRelation($idEntity);
            Timeline::addTimeline("qualia",$this->getId(),"S");
            parent::delete();
            $entity = new Entity($idEntity);
            $entity->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteRelation($idRelation)
    {
        $transaction = $this->beginTransaction();
        try {
            $relation = new EntityRelation($idRelation);
            $relation->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function updateRelation($idRelation)
    {
        $transaction = $this->beginTransaction();
        try {
            $relation = new EntityRelation($idRelation);
            $relation->setIdEntity3($this->getIdEntity());
            $relation->save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function updateEntry($newEntry)
    {
        $transaction = $this->beginTransaction();
        try {
//            Base::updateTimeLine($this->getEntry(), $newEntry);
            $entity = new Entity($this->getIdEntity());
            $entity->setAlias($newEntry);
            $entity->save();
            $entry = new Entry();
            $entry->updateEntry($this->getEntry(), $newEntry);
            $this->setEntry($newEntry);
            parent::save();
            Timeline::addTimeline("qualia",$this->getId(),"S");
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }
}
