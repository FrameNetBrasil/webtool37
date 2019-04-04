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

class Genre extends map\GenreMap {

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

    public function listAll()
    {
        $criteria = $this->getCriteria()->select('idGenre, entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        return $criteria;
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('idGenre,idGenreType, entry, entries.name name')->orderBy('idGenre');
        if ($filter->idGenre){
            $criteria->where("idGenre = {$filter->idGenre}");
        }
        if ($filter->idGenreType){
            $criteria->where("idGenreType = {$filter->idGenreType}");
        }
        if ($filter->entry){
            $criteria->where("entry LIKE '%{$filter->entry}%'");
        }
        Base::entryLanguage($criteria);
        return $criteria;
    }
    
    public function save($data)
    {
        $this->setData($data);
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
