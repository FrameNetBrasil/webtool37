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

class Template extends map\TemplateMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'entry' => array('notnull'),
                'active' => array('notnull'),
                'idEntity' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getEntry();
    }

    public function getEntryObject() {
        $criteria = $this->getCriteria()->select('entries.name, entries.description, entries.nick');
        $criteria->where("idTemplate = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->asObjectArray()[0];
    }
    
    public function getName() {
        $criteria = $this->getCriteria()->select('entries.name as name');
        $criteria->where("idTemplate = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->fields('name');
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('idTemplate, entry, active, idEntity, entries.name as name, relations.name as frameName')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        if ($filter->idTemplate) {
            $criteria->where("idTemplate = {$filter->idTemplate}");
        }
        if ($filter->fe) {
            $criteriaFE = FrameElement::getCriteria();
            $criteriaFE->select('template.idTemplate, entries.name as name');
            $criteriaFE->where("entries.name LIKE '{$filter->fe}%'");
            Base::entryLanguage($criteriaFE);
            Base::relation($criteriaFE, 'FrameElement', 'tTemplate', 'rel_elementof');
            $criteria->distinct(true);
            $criteria->tableCriteria($criteriaFE,'fe');
            $criteria->where("idTemplate = fe.idTemplate");
        }
        if ($filter->frame) {
            $criteria->where("entries.name LIKE '{$filter->frame}%'");
        }
        $criteria2 = Base::relationCriteria('template', 'frame', 'rel_createdfrom', 'Template.idTemplate, Frame.entries.name as name');
        Base::entryLanguage($criteria2, 'Frame');
        //$criteria2->asQuery()->getResult();
        $criteria2->setAlias('relations');
        $criteria->joinCriteria($criteria2, "relations.idTemplate = template.idTemplate", "LEFT");
        
        return $criteria;
    }

    public function listFE()
    {
        $fe = new FrameElement();
        $criteria = $fe->getCriteria()->select('idFrameElement, entry, entries.name as name, typeinstance.entry as coreType, color.rgbFg, color.rgbBg');
        Base::entryLanguage($criteria);
        Base::relation($criteria, 'FrameElement', 'Template', 'rel_elementof');
        Base::relation($criteria, 'FrameElement', 'TypeInstance', 'rel_hastype');
        $criteria->where("template.idTemplate = {$this->idTemplate}");
        $criteria->orderBy('typeinstance.idTypeInstance, entries.name');
        return $criteria;
    }

    public function listFEforNewFrame()
    {
        $fe = new FrameElement();
        $criteria = $fe->getCriteria()->select('idFrameElement, entry, typeinstance.idTypeInstance as idCoreType, idColor, idEntity');
        Base::relation($criteria, 'FrameElement', 'Template', 'rel_elementof');
        Base::relation($criteria, 'FrameElement', 'TypeInstance', 'rel_hastype');
        $criteria->where("template.idTemplate = {$this->idTemplate}");
        return $criteria;
    }

    public function listFEforDeletion()
    {
        $fe = new FrameElement();
        $criteria = $fe->getCriteria()->select('idFrameElement, idEntity');
        Base::relation($criteria, 'FrameElement', 'Template', 'rel_elementof');
        $criteria->where("template.idTemplate = {$this->idTemplate}");
        return $criteria;
    }

    public function listAll($idLanguage)
    {
        $criteria = $this->getCriteria()->select('*, entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        return $criteria;
    }
    
    public function listForLookup()
    {
        $criteria = $this->getCriteria()->select('idTemplate,entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        return $criteria;
    }

    public function listTemplatedFrames()
    {
        $frame = new Frame();
        $criteria = $frame->getCriteria()->select('idFrame, entries.name as name, entries.description as description')->orderBy('entries.name');
        Base::relation($criteria, 'Frame', 'Template', 'rel_hastemplate');
        $criteria->where("template.idEntity = {$this->getIdEntity()}");
        Base::entryLanguage($criteria);
        return $criteria;
    }
    
    public function listTemplatedFEs($idFETemplate)
    {
        $frameElement = new FrameElement();
        $criteria = $frameElement->getCriteria()->select('fe1.idFrameElement, fe1.entries.name as name, fe1.entries.description as description')->orderBy('fe1.entries.name');
        Base::entryLanguage($criteria,'frameelement');
        Base::relation($criteria, 'FrameElement fe1', 'FrameElement fe2', 'rel_hastemplate');
        $criteria->where("fe2.idFrameElement = {$idFETemplate}");
        return $criteria;
    }

    public function getBaseFrame()
    {
        // if template was created from a frame, this frame  is the "base frame" 
        $frame = new Frame();
        $criteria = $frame->getCriteria()->select('idFrame, entries.name as name, entries.description as description')->orderBy('entries.name');
        Base::relation($criteria, 'Template', 'Frame', 'rel_createdfrom');
        $criteria->where("template.idEntity = {$this->getIdEntity()}");
        Base::entryLanguage($criteria);
        return $criteria;
    }
    
    public function createFromFrame($idFrame) {
        $transaction = $this->beginTransaction();
        try {
            $frame = new Frame($idFrame);
            $this->setEntry('tpl_' . strtolower(str_replace('frm_','', $frame->getEntry())));
            $this->save();
            Base::createEntityRelation($this->getIdEntity(), 'rel_createdfrom', $frame->getIdEntity());
            $fes = $frame->listFE()->asQuery()->asObjectArray();
            $fe = new FrameElement();
            foreach($fes as $feData) {
                $fe->setPersistent(false);
                $feEntry = $this->getEntry() . '_' . $feData->entry;
                $entry = new Entry();
                $entry->cloneEntry($feData->entry, $feEntry);
                $fe->setEntry($feData->entry);
                $entity = new Entity();
                $entity->setAlias($feEntry);
                $entity->setType('FE');
                $entity->save();
                Base::createEntityRelation($entity->getId(), 'rel_elementof', $this->getIdEntity());
                $coreType = new TypeInstance($feData->idCoreType);
                Base::createEntityRelation($entity->getId(), 'rel_hastype', $coreType->getIdEntity());
                $fe->setIdEntity($entity->getId());
                $fe->setActive(true);
                $fe->setIdColor($feData->idColor);
                $fe->saveModel();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function save()
    {
        $transaction = $this->beginTransaction();
        try {
            $entity = new Entity();
            $entity->setAlias($this->getEntry());
            $entity->setType('TP');
            $entity->save();
            $entry = new Entry();
            $entry->newEntry($this->getEntry());
            $this->setIdEntity($entity->getId());
            $this->setActive(true);
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
        $transaction = $this->beginTransaction();
        try {
            $idTemplate = $this->getId();
            $idEntity = $this->getIdEntity();
            // remove entry
            $entry = new Entry();
            $entry->deleteEntry($this->getEntry());
            // remove related FEs
            //Base::deleteEntity2Relation($idEntity, 'rel_elementof');
            $fe = new FrameElement();
            $fes = $this->listFEforDeletion()->asQuery()->getResult();
            foreach($fes as $row) {
                $fe->getById($row['idFrameElement']);
                $fe->delete();
            }
            Base::entityTimelineDelete($this->getIdEntity());
            // remove this template
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

    public function updateEntry($newEntry)
    {
        $transaction = $this->beginTransaction();
        try {
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

    
}

