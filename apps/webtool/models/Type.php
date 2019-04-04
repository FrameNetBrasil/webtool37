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

class Type extends map\TypeMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'entry' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getEntry();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idType');
        if ($filter->idType){
            $criteria->where("idType = {$filter->idType}");
        }
        if ($filter->entry){
            $criteria->where("entry LIKE '%{$filter->entry}%'");
        }
        return $criteria;
    }
    
    public function getInstantiationType($entry = '') {
        $criteria = $this->getCriteria();
        $criteria->select("typeinstances.idTypeInstance as idInstantiationType,  typeinstances.entries.name as instantiationType");
        $criteria->where("entry = 'typ_instantiationtype'");
        if ($entry != '') {
            $criteria->where("typeinstances.entry = '{$entry}'");
        }
        Base::entryLanguage($criteria, 'typeinstances');
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

    
    
}
