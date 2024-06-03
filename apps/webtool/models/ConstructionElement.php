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
            $criteria->where("idConstructionElement = {$filter->idConstructionElement}");
        }
        if ($filter->idConstruction) {
            //Base::relation($criteria, 'ConstructionElement', 'Construction', 'rel_elementof');
            $criteria->where("idConstruction = {$filter->idConstruction}");
        }          
        return $criteria;
    }

    public function listForLookupInhName($idConstructionElement, $name = '')
    {
        $criteria = $this->getCriteria()->select("idConstructionElement, concat(entries.name, ' [', language.language,']') as name")->orderBy('entries.name');
        $idLanguage = \Manager::getSession()->idLanguage;
        $cmd = <<<HERE

        SELECT ceParent.idConstructionElement, concat(e1.name, '.', e2.name, ' [',language.language,']') as name
        FROM ConstructionElement ceBase
            INNER JOIN Construction base
                ON (ceBase.idConstruction = base.idConstruction)
            INNER JOIN EntityRelation
                ON (base.idEntity = EntityRelation.idEntity2)
            INNER JOIN RelationType
                ON (EntityRelation.idRelationType = RelationType.idRelationType)
            INNER JOIN Construction parent
                ON (parent.idEntity = EntityRelation.idEntity1)
            INNER JOIN ConstructionElement ceParent
                ON (ceParent.idConstruction = parent.idConstruction)
            INNER JOIN Entry 
                ON (Entry.entry = base.entry)
            INNER JOIN Entry e1 
                ON (e1.entry = parent.entry)
            INNER JOIN Entry  e2
                ON (e2.entry = ceParent.entry)
            INNER JOIN language  
                ON (Entry.idLanguage = language.idLanguage)
        WHERE (ceBase.idConstructionElement = {$idConstructionElement})
            AND (RelationType.entry in ('rel_inheritance_cxn'))
           AND (Entry.idLanguage = {$idLanguage} )
           AND (e1.idLanguage = {$idLanguage} )
           AND (e2.idLanguage = {$idLanguage} )

HERE;
        $result = $this->getDb()->getQueryCommand($cmd);
        return $result;
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

        
SELECT entry, name, nick, idEntity, idConcept, conceptEntry,idEntityRelation
FROM (        
        SELECT RelationType.entry, entry_relatedConcept.name, entry_relatedConcept.nick, relatedConcept.idEntity, relatedConcept.idConcept idConcept, relatedConcept.entry as conceptEntry, EntityRelation.idEntityRelation
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
                'rel_hasconcept'))
           AND (entry_relatedConcept.idLanguage = {$idLanguage} )
) evokes
UNION
SELECT entry, name, nick, idEntity, idFrameElement, frameElementEntry,idEntityRelation
FROM (
        SELECT RelationType.entry, concat(entry_relatedFrame.name,'.',entry_relatedFE.name) as name, entry_relatedFE.nick, relatedFE.idEntity, relatedFE.idFrameElement idFrameElement, relatedFE.entry as frameElementEntry, EntityRelation.idEntityRelation
        FROM ConstructionElement
            INNER JOIN Entity entity1
                ON (ConstructionElement.idEntity = entity1.idEntity)
            INNER JOIN EntityRelation
                ON (entity1.idEntity = EntityRelation.idEntity1)
            INNER JOIN RelationType
                ON (EntityRelation.idRelationType = RelationType.idRelationType)
            INNER JOIN Entity entity2
                ON (EntityRelation.idEntity2 = entity2.idEntity)
            INNER JOIN FrameElement relatedFE
                ON (entity2.idEntity = relatedFE.idEntity)
            INNER JOIN Entry entry_relatedFE
                ON (relatedFE.entry = entry_relatedFE.entry)
            INNER JOIN Frame relatedFrame
                ON (relatedFE.idFrame = relatedFrame.idFrame)
            INNER JOIN Entry entry_relatedFrame
                ON (relatedFrame.entry = entry_relatedFrame.entry)
        WHERE (ConstructionElement.idConstructionElement = {$this->getId()})
            AND (RelationType.entry in (
                'rel_evokes'))
           AND (entry_relatedFE.idLanguage = {$idLanguage} )
           AND (entry_relatedFrame.idLanguage = {$idLanguage} )
) evokesFE
ORDER BY entry, name
            
HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->treeResult('entry', 'name,idEntity,idConcept,conceptEntry,idEntityRelation');
        return $result;

    }

    public function listInheritanceRelations()
    {
        $idLanguage = \Manager::getSession()->idLanguage;
        $cmd = <<<HERE

        
SELECT entry, name, nick, idEntity, idCE, ceEntry, idEntityRelation
FROM (
        SELECT RelationType.entry,concat(entry_relatedCXN.name, '.', entry_relatedCE.name) name, entry_relatedCE.nick, ce1.idEntity, ce1.idConstructionElement idCE, ce1.entry as ceEntry, EntityRelation.idEntityRelation
        FROM ConstructionElement ce1
            INNER JOIN EntityRelation
                ON (ce1.idEntity = EntityRelation.idEntity1)
            INNER JOIN RelationType
                ON (EntityRelation.idRelationType = RelationType.idRelationType)
            INNER JOIN ConstructionElement ce2
                ON (ce2.idEntity = EntityRelation.idEntity2)
            INNER JOIN Construction parent
                ON (ce1.idConstruction = parent.idConstruction)
            INNER JOIN Entry entry_relatedCE
                ON (ce1.entry = entry_relatedCE.entry)
            INNER JOIN Entry entry_relatedCXN
                ON (parent.entry = entry_relatedCXN.entry)
        WHERE (ce2.idConstructionElement = {$this->getId()})
            AND (RelationType.entry in (
                'rel_inheritance_cxn'))
           AND (entry_relatedCE.idLanguage = {$idLanguage} )
           AND (entry_relatedCXN.idLanguage = {$idLanguage} )
) inheritance           
ORDER BY entry, name
            
HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->treeResult('entry', 'name,idEntity,idCE,ceEntry,idEntityRelation');
        return $result;

    }

    public function getStylesByCxn($idConstruction)
    {
        $criteria = $this->getCriteria()->select('idConstructionElement, entry, entries.name as name, color.rgbFg, color.rgbBg');
        Base::entryLanguage($criteria);
        //Base::relation($criteria, 'ConstructionElement', 'Construction', 'rel_elementof');
        $criteria->where("idConstruction = {$idConstruction}");
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
            //Base::relation($criteria, 'ConstructionElement', 'Construction', 'rel_elementof');
            $criteria->where("idConstruction = {$idConstruction}");
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
                if ($this->getEntry() != $data->entry) {
                    $entity = new Entity($this->getIdEntity());
//                    Base::updateTimeLine($this->getEntry(), $data->entry);
                    $entity->setAlias($data->entry);
                    $entity->save();
                    $entry->updateEntry($this->getEntry(), $data->entry, $data->name);
                    $entry->setIdEntity($entity->getId());
                }
            } else {
                $entity = new Entity();
                $entity->setAlias($data->entry);
                $entity->setType('CE');
                $entity->save();
                $entry = new Entry();
                $entry->newEntry($data->entry, $entity->getId(), $data->name);
                Base::createEntityRelation($entity->getId(), 'rel_elementof', $schema->getIdEntity());
		$this->setIdEntity($entity->getId());
                $this->setIdConstruction($schema->getIdConstruction());
            }
            $this->setData($data);
            $this->setActive(true);
            parent::save();
            Timeline::addTimeline("constructionelement",$this->getId(),"S");
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }
    
    public function saveModel(){
        parent::save();
        Timeline::addTimeline("constructionelement",$this->getId(),"S");
    }

    public function updateEntry($newEntry)
    {
        $transaction = $this->beginTransaction();
        try {
//            Base::updateTimeLine($this->getEntry(), $newEntry);
            Timeline::addTimeline("constructionelement",$this->getId(),"S");
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
//            Base::entityTimelineDelete($this->getIdEntity());
            // remove this ce
            Timeline::addTimeline("constructionelement",$this->getId(),"D");
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
        Timeline::addTimeline("constructionelement",$this->getId(),"S");
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

