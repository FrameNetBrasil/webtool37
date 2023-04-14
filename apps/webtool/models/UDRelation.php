<?php
/**
 *
 *
 * @category   Maestro
 * @package    UFJF
 * @subpackage fnbr
 * @copyright  Copyright (c) 2003-2012 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version
 * @since
 */

namespace fnbr\models;

class UDRelation extends map\UDRelationMap
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

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('idTypeInstance');
        if ($filter->idTypeInstance) {
            $criteria->where("idTypeInstance LIKE '{$filter->idTypeInstance}%'");
        }
        return $criteria;
    }

    public function listForLookup($type)
    {
        $whereType = ($type == '*') ? '' : "WHERE (t.entry = '{$type}')";
        $cmd = <<<HERE
        SELECT u.idUDRelation, u.info
        FROM UDRelation u
        JOIN TypeInstance t on (u.idTypeInstance = t.idTypeInstance)
        {$whereType} 
        ORDER BY u.info

HERE;
        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function listForLookupEntity($type)
    {
        $whereType = ($type == '*') ? '' : "WHERE (t.entry = '{$type}')";
        $cmd = <<<HERE
        SELECT u.idEntity, u.info
        FROM UDRelation u
        JOIN TypeInstance t on (u.idTypeInstance = t.idTypeInstance)
        {$whereType} 
        ORDER BY u.info

HERE;
        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    /*
        public function saveData($data)
        {
            $transaction = $this->beginTransaction();
            try {
                $frame = new Frame($data->idFrame);
                $alias = $data->type . '_' . $data->idFrame . '_' . $data->idFE1 . '_' . $data->idFE2;
                $entity = new Entity();
                $entity->setAlias($alias);
                $entity->setType('QR');  // Qualia Relation
                $entity->save();
                Base::createEntityRelation($entity->getId(), 'rel_qualia_frame', $frame->getIdEntity());
                $fe1 = new FrameElement($data->idFE1);
                $fe2 = new FrameElement($data->idFE2);
                Base::createEntityRelation($entity->getId(), 'rel_qualia_lu1_fe', $fe1->getIdEntity());
                Base::createEntityRelation($entity->getId(), 'rel_qualia_lu2_fe', $fe2->getIdEntity());
                $this->setIdEntity($entity->getId());
                Base::entityTimelineSave($this->getIdEntity());
                $typeInstance = new TypeInstance();
                $this->setIdTypeInstance($typeInstance->getIdQualiaTypeByEntry($data->type));
                $this->setInfo($data->info);
                parent::save();
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
                Base::entityTimelineDelete($idEntity);
                Base::deleteAllEntityRelation($idEntity);
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
    */
}
