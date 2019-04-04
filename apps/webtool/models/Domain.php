<?php

/**
 * 
 *
 * @category   Maestro
 * @package    UFJF
 *  @subpackage fnbr
 * @copyright  Copyright (c) 2003-2012 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version    
 * @since      
 */

namespace fnbr\models;

class Domain extends map\DomainMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'entry' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription()
    {
        return $this->getEntry();
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('entry');
        if ($filter->idDomain) {
            $criteria->where("idDomain = {$filter->idDomain}");
        }
        if ($filter->entry) {
            $criteria->where("entry LIKE '%{$filter->entry}%'");
        }
        return $criteria;
    }

    public function listAll()
    {
        $criteria = $this->getCriteria()->select('idDomain, entries.name as name, idEntity')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        return $criteria;
    }

    public function listForSelection()
    {
        $criteria = $this->getCriteria()->select('idDomain, entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        $criteria->orderBy('entries.name');
        return $criteria;
    }

    public function save() {
        $transaction = $this->beginTransaction();
        try {
            $idEntity = $this->getIdEntity();
            $entity = new Entity($idEntity);
            $entity->setAlias($this->getEntry());
            $entity->setType('DO');
            $entity->save();
            $this->setIdEntity($entity->getId());
            $entry = new Entry();
            $entry->newEntry($this->getEntry());
            Base::entityTimelineSave($this->getIdEntity());
            parent::save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function addEntity($idEntity, $relation = 'rel_hasdomain') {
        Base::createEntityRelation($idEntity, $relation, $this->getIdEntity());
    }

    public function delDomainFromEntity($idEntity, $idDomainEntity = [], $relation = 'rel_hasdomain') {
        $rt = new RelationType();
        $c = $rt->getCriteria()->select('idRelationType')->where("entry = '{$relation}'");
        $er = new EntityRelation();
        $transaction = $er->beginTransaction();
        $criteria = $er->getDeleteCriteria();
        $criteria->where("idEntity1 = {$idEntity}");
        $criteria->where("idEntity2","IN",$idDomainEntity);
        $criteria->where("idRelationType", "=", $c);
        $criteria->delete();
        $transaction->commit();
    }


}
