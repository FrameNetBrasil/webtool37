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

class LayerType extends map\LayerTypeMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'entry' => array('notnull'),
                'allowsApositional' => array('notnull'),
                'isAnnotation' => array('notnull'),
                'idLayerGroup' => array('notnull'),
                'idEntity' => array('notnull'),
                'idLanguage' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription()
    {
        return $this->getEntry();
    }

    public function getName() {
        $criteria = $this->getCriteria()->select('entries.name as name');
        $criteria->where("idLayerType = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->fields('name');
    }
    
    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('idLayerType');
        if ($filter->idLayerType) {
            $criteria->where("idLayerType LIKE '{$filter->idLayerType}%'");
        }
        return $criteria;
    }

    public function listAll()
    {
        $criteria = $this->getCriteria()->select('idLayerType, entry, allowsApositional, isAnnotation, layerOrder, idLayerGroup, idEntity, entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        return $criteria;
    }
    
    public function listByGroup()
    {
        $criteria = $this->getCriteria()->select('idLayerType, entries.name name')->orderBy('idLayerGroup, entries.name');
        Base::entryLanguage($criteria);
        return $criteria;
    }

    public function listLabelType($filter)
    {
        $idLanguage = \Manager::getSession()->idLanguage;
        $criteria = $this->getCriteria()->select('idLayerType, entry, genericlabel.name as labelType, genericlabel.idEntity as idLabelType')
                ->orderBy('entry, genericlabel.name');
        Base::relation($criteria, 'LayerType', 'GenericLabel', 'rel_haslabeltype');
        $criteria->where("genericlabel.idLanguage = {$idLanguage}");
        if ($filter->entry) {
            $criteria->where("entry = '{$filter->entry}'");
        }
        return $criteria;
    }

    public function getByEntry($entry) {
        $criteria = $this->getCriteria()->select('*, entries.name name')->orderBy('idLayerType');
        $criteria->where("entry = '{$entry}'");
        $this->retrieveFromCriteria($criteria);
    }
    
    public function listToLU($lu) {
        $array = array('lty_fe','lty_gf','lty_pt', 'lty_other','lty_target','lty_sent');
        $lPOS = array('V'=>'lty_verb','N' => 'lty_noun','A' => 'lty_adj','ADV'=>'lty_adv','P'=>'lty_prep');
        $pos = $lu->getPOS();
        $array[] = $lPOS[$pos];
        $criteria = $this->getCriteria();
        $criteria->select('idLayerType, entry');
        $criteria->where('entry','in', $array);
        $result = $criteria->asQuery()->getResult();
        return $result;
    }

    public function listToConstruction() {
        $array = array('lty_ce','lty_cee','lty_cstrpt','lty_other','lty_sent','lty_govx','lty_udpos','lty_udrelation'); // ? 'lty_pt','lty_gf',
        $criteria = $this->getCriteria();
        $criteria->select('idLayerType, entry');
        $criteria->where('entry','in', $array);
        $result = $criteria->asQuery()->getResult();
        return $result;
    }
    
    public function listCEFE() {
        $array = array('lty_cefe'); 
        $criteria = $this->getCriteria();
        $criteria->select('idLayerType, entry');
        $criteria->where('entry','in', $array);
        $result = $criteria->asQuery()->getResult();
        return $result;
    }
    
    public function save($data)
    {
        $data->allowsApositional = $data->allowsApositional ?: '0'; 
        $data->isAnnotation = $data->isAnnotation ?: '0'; 
        $transaction = $this->beginTransaction();
        try {
            $this->setData($data);
            if (!$this->isPersistent()) {
                $entity = new Entity();
                $entity->setAlias($data->entry);
                $entity->setType('LT');
                $entity->save();
                $this->setIdEntity($entity->getId());
                $entry = new Entry();
                $entry->newEntry($data->entry);
            }
            Base::entityTimelineSave($this->getIdEntity());
            parent::save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete()
    {
        Base::entityTimelineDelete($this->getIdEntity());
        parent::delete();
    }


}

