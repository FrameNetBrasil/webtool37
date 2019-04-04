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

class ConstructionElement extends map\ConstructionElementMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'entry' => array('notnull'),
                'active' => array('notnull'),
                'idEntity' => array('notnull'),
                'idColor' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription(){
        return $this->getIdConstructionElement();
    }

    public function getData()
    {
        $data = parent::getData();
        $data = (object)array_merge((array)$data, (array)$this->getEntryObject());
        $construction = $this->getConstruction();
        $data->idConstruction = $construction->getIdConstruction();
        return $data;
    }

    public function getEntryObject()
    {
        $criteria = $this->getCriteria()->select('entries.name, entries.description, entries.nick');
        $criteria->where("idConstructionElement = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->asObjectArray()[0];
    }

    public function getName()
    {
        $criteria = $this->getCriteria()->select('entries.name as name');
        $criteria->where("idConstructionElement = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->fields('name');
    }

    public function getConstruction() {
        $vc = new ViewConstruction();
        $criteria = $vc->getCriteria()->select('idConstruction')->where("ces.idConstructionElement = {$this->getId()}");
        return Construction::create($criteria->asQuery()->getResult()[0]['idConstruction']);
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idConstructionElement');
        if ($filter->idConstructionElement){
            $criteria->where("idConstructionElement LIKE '{$filter->idConstructionElement}%'");
        }
        if ($filter->idConstruction) {
            Base::relation($criteria, 'ConstructionElement', 'Construction', 'rel_elementof');
            $criteria->where("construction.idConstruction = {$filter->idConstruction}");
        }          
        return $criteria;
    }
    
    public function listForEditor($idEntityCxn)
    {
        $criteria = $this->getCriteria()->select('idEntity,entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        Base::relation($criteria, 'ConstructionElement', 'Construction', 'rel_elementof');
        $criteria->where("Construction.idEntity = {$idEntityCxn}");
        return $criteria;
    }


    public function listForExport($idCxn)
    {
        $view = new ViewConstructionElement();
        $criteria = $view->listForExport($idCxn);
        return $criteria;
    }

    public function listSiblingsCE()
    {
        $view = new ViewConstructionElement();
        $query = $view->listSiblingsCE($this->getId());
        return $query;
    }

    public function listConstraints()
    {
        $constraint = new ViewConstraint();
        return $constraint->getByIdConstrained($this->getIdEntity());
    }

    public function listDirectRelations()
    {
        $idLanguage = \Manager::getSession()->idLanguage;
        $cmd = <<<HERE

        SELECT RelationType.entry, entry_relatedCE.name, relatedCE.idEntity, relatedCE.idConstructionElement, entry_relatedCE.entry as ceEntry
        FROM ConstructionElement
            INNER JOIN Entity entity1
                ON (ConstructionElement.idEntity = entity1.idEntity)
            INNER JOIN EntityRelation
                ON (entity1.idEntity = EntityRelation.idEntity1)
            INNER JOIN RelationType 
                ON (EntityRelation.idRelationType = RelationType.idRelationType)
            INNER JOIN Entity entity2
                ON (EntityRelation.idEntity2 = entity2.idEntity)
            INNER JOIN ConstructionElement relatedCE
                ON (entity2.idEntity = relatedCE.idEntity)
            INNER JOIN Entry entry_relatedCE
                ON (relatedCE.entry = entry_relatedCE.entry)
        WHERE (ConstructionElement.idConstructionElement = {$this->getId()})
            AND (RelationType.entry in (
                'rel_inheritance_cxn', 'rel_inhibits'))
           AND (entry_relatedCE.idLanguage = {$idLanguage} )
        ORDER BY RelationType.entry, entry_relatedCE.name
            
HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->treeResult('entry', 'name,idEntity,idConstructionElement,ceEntry');
        return $result;

    }

    public function listEvokesRelations()
    {
        $idLanguage = \Manager::getSession()->idLanguage;
        $cmd = <<<HERE

        
SELECT entry, name, nick, idEntity, idConcept, conceptEntry
FROM (        
        SELECT RelationType.entry, entry_relatedConcept.name, entry_relatedConcept.nick, relatedConcept.idEntity, relatedConcept.idConcept idConcept, relatedConcept.entry as conceptEntry
        FROM ConstructionElement
            INNER JOIN Entity entity1
                ON (ConstructionElement.idEntity = entity1.idEntity)
            INNER JOIN EntityRelation
                ON (entity1.idEntity = EntityRelation.idEntity1)
            INNER JOIN RelationType 
                ON (EntityRelation.idRelationType = RelationType.idRelationType)
            INNER JOIN Entity entity2
                ON (EntityRelation.idEntity2 = entity2.idEntity)
            INNER JOIN Concept relatedConcept
                ON (entity2.idEntity = relatedConcept.idEntity)
            INNER JOIN Entry entry_relatedConcept
                ON (relatedConcept.entry = entry_relatedConcept.entry)
        WHERE (ConstructionElement.idConstructionElement = {$this->getId()})
            AND (RelationType.entry in (
                'rel_evokes'))
           AND (entry_relatedConcept.idLanguage = {$idLanguage} )
) evokes           
ORDER BY entry, name
            
HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->treeResult('entry', 'name,idEntity,idConcept,conceptEntry');
        return $result;

    }


    public function getStylesByCxn($idConstruction)
    {
        $criteria = $this->getCriteria()->select('idConstructionElement, entry, entries.name as name, color.rgbFg, color.rgbBg');
        Base::entryLanguage($criteria);
        Base::relation($criteria, 'ConstructionElement', 'Construction', 'rel_elementof');
        $criteria->where("idConstruction = '{$idConstruction}'");
        $result = $criteria->asQuery()->getResult();
        $styles = [];
        foreach ($result as $ce) {
            $name = strtolower($ce['name']);//
            $styles[$name] = ['ce' => $name, 'rgbFg' => $ce['rgbFg'], 'rgbBg' => $ce['rgbBg']];
        }
        return $styles;
    }


    public function listForReport($idConstruction = '')
    {
        $criteria = $this->getCriteria()->select('idConstructionElement,entries.name as name, entries.description as description, entries.nick as nick')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        if ($idConstruction) {
            Base::relation($criteria, 'ConstructionElement', 'Construction', 'rel_elementof');
            $criteria->where("construction.idConstruction = {$idConstruction}");
        }
        return $criteria;
    }

    public function setData($data) {
        $data->optional = $data->optional ?: 0;
        $data->multiple = $data->multiple ?: 0;
        $data->head = $data->head ?: 0;
        parent::setData($data);
    }

    public function save($data)
    {
        $schema = new Construction($data->idConstruction);
        $data->entry = 'ce_' . mb_strtolower(str_replace('cxn_', '', $schema->getEntry())) . '_' . mb_strtolower(str_replace('ce_', '', $data->name));
        $data->optional = $data->optional ?: false;
        $data->head = $data->head ?: false;
        $data->multiple = $data->multiple ?: false;
        $transaction = $this->beginTransaction();
        try {
            $entry = new Entry();
            if ($this->isPersistent()) {
                mdump('========== persistent!');
                if ($this->getEntry() != $data->entry) {
                    $entity = new Entity($this->getIdEntity());
                    Base::updateTimeLine($this->getEntry(), $data->entry);
                    $entity->setAlias($data->entry);
                    $entity->save();
                    $entry->updateEntry($this->getEntry(), $data->entry, $data->name);
                }
            } else {
                mdump('========== NOT persistent!');
                $entity = new Entity();
                $entity->setAlias($data->entry);
                $entity->setType('CE');
                $entity->save();
                $entry = new Entry();
                $entry->newEntry($data->entry, $data->name);
                $this->setIdEntity($entity->getId());
                Base::createEntityRelation($entity->getId(), 'rel_elementof', $schema->getIdEntity());
            }
            $this->setData($data);
            $this->setActive(true);
            parent::save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }
    
    public function saveModel(){
        parent::save();
    }

    public function updateEntry($newEntry)
    {
        $transaction = $this->beginTransaction();
        try {
            Base::updateTimeLine($this->getEntry(), $newEntry);
            $entity = new Entity($this->getIdEntity());
            $entity->setAlias($newEntry);
            $entity->save();
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

    public function delete() {
        $transaction = $this->beginTransaction();
        try {
            $idEntity = $this->getIdEntity();
            // remove entry
            $entry = new Entry();
            $entry->deleteEntry($this->getEntry());
            // remove ce-relations
            Base::deleteAllEntityRelation($idEntity);
            Base::entityTimelineDelete($this->getIdEntity());
            // remove this ce
            parent::delete();
            // remove entity
            $entity = new Entity($idEntity);
            $entity->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }


    public function createFromData($data)
    {
        $this->setPersistent(false);
        $this->setEntry($data->entry);
        $this->setActive($data->active);
        $this->setIdEntity($data->idEntity);
        $this->setIdColor($data->idColor);
        $this->setOptional($data->optional);
        $this->setHead($data->head);
        $this->setMultiple($data->multiple);
        parent::save();
    }

    public function createRelationsFromData($data)
    {
        if ($data->idConstruction) {
            $cxn = new Construction($data->idConstruction);
            if ($cxn->getIdEntity()) {
                Base::createEntityRelation($this->getIdEntity(), 'rel_elementof', $cxn->getIdEntity());
            }
        }
    }
}

