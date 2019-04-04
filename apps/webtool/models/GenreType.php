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

class GenreType extends map\GenreTypeMap
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

    public function getEntryObject()
    {
        $criteria = $this->getCriteria()->select('entries.name, entries.description, entries.nick');
        $criteria->where("idGenreType = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->asObjectArray()[0];
    }

    public function getName()
    {
        $criteria = $this->getCriteria()->select('entries.name as name');
        $criteria->where("idGenreType = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->getResult()[0]['name'];
    }

    public function listAll()
    {
        $criteria = $this->getCriteria()->select('idGenreType, entries.name as name')->orderBy('entry');
        Base::entryLanguage($criteria);
        return $criteria;
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria();
        $criteria->setAssociationAlias('entries', 'centry');
        $criteria->select('distinct idGenreType, entry, centry.name as name')->orderBy('centry.name');
        Base::entryLanguage($criteria);
        if ($filter->idGenreType) {
            $criteria->where("idGenreType = {$filter->idGenrType}");
        }
        if ($filter->genreType) {
            $criteria->where("upper(centry.name) LIKE upper('%{$filter->genreType}%')");
        }
        if ($filter->entry) {
            $criteria->where("upper(entry) LIKE upper('%{$filter->entry}%')");
        }
        return $criteria;
    }

    public function save($data)
    {
        $this->setData($data);
        $transaction = $this->beginTransaction();
        try {
            $entry = new Entry();
            $entry->newEntry($this->getEntry());
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
            Base::updateTimeLine($this->getEntry(), $newEntry);
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
