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

class Relationgroup extends map\RelationGroupMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getEntry();
    }
    
    public function getName() {
        $criteria = $this->getCriteria()->select('entries.name as name');
        $criteria->where("idRelationGroup = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->fields('name');
    }
    

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idRelationGroup');
        if ($filter->idRelationGroup){
            $criteria->where("idRelationGroup = {$filter->idRelationGroup}");
        }
        if ($filter->entry) {
            $criteria->where("entry LIKE '%{$filter->entry}%'");
        }
        return $criteria;
    }
    
    public function listAll(){
        $criteria = $this->getCriteria()->select('idRelationGroup, entry, entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        return $criteria;
    }
    
    public function save($data)
    {
        $transaction = $this->beginTransaction();
        try {
            if (!$this->isPersistent()) {
                $entry = new Entry();
                $entry->newEntry($this->getEntry());
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

