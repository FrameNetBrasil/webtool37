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

class RelationType extends map\RelationTypeMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'entry' => array('notnull'),
                'nameEntity1' => array('notnull'),
                'nameEntity2' => array('notnull'),
                'idDomain' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdRelationType();
    }

    public function getName() {
        $criteria = $this->getCriteria()->select('entries.name as name');
        $criteria->where("idRelationType = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->fields('name');
    }
    
    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('idRelationType,entry,entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        if ($filter->idRelationType){
            $criteria->where("idRelationType = {$filter->idRelationType}");
        }
        if ($filter->entry){
            $criteria->where("entry = '{$filter->entry}'");
        }
        if ($filter->group){
            $criteria->where("relationgroup.entry = '{$filter->group}'");
        }
        return $criteria;
    }
    
    public function listAll(){
        $criteria = $this->getCriteria()->select('idRelationType, entry, nameEntity1, nameEntity2, entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        return $criteria;
    }

    public function getByEntry($entry){
        $criteria = $this->getCriteria()->select('*')->where("entry = '{$entry}'");
        $this->retrieveFromCriteria($criteria);
    }
    
    public function save($data)
    {
        $transaction = $this->beginTransaction();
        try {
            if (!$this->isPersistent()) {
                $entry = new Entry();
                $entry->newEntry($this->getEntry());
                $translation = new Translation();
                $translation->newResource($this->getNameEntity1());
                $translation->newResource($this->getNameEntity2());
            }
            parent::save();
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
            $entry = new Entry();
            $entry->updateEntry($this->getEntry(), $newEntry);
            $this->setEntry($newEntry);
            parent::save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }    
    
    
}
