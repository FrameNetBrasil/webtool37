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

class FrameElement extends map\FrameElementMap
{

    private $idCoreType;
    private $idFrame;

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'entry' => array('notnull'),
                'active' => array('notnull'),
                'idEntity' => array('notnull'),
                'idColor' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getIdCoreType()
    {
        return $this->idCoreType;
    }

    public function setIdCoreType($value)
    {
        $this->idCoreType = (int) $value;
    }

    public function getIdFrame()
    {
        return $this->idFrame;
    }

    public function setIdFrame($value)
    {
        $this->idFrame = (int) $value;
    }

    public function getDescription()
    {
        return $this->getIdFrameElement();
    }

    public function getData()
    {
        $data = parent::getData();
        $data->idCoreType = $this->idCoreType;
        $data->idFrame = $this->idFrame;
        return $data;
    }

    public function setData($data)
    {
        parent::setData($data);
        $this->idCoreType = $data->idCoreType;
        $this->idFrame = $data->idFrame;
    }

    public function getName()
    {
        $criteria = $this->getCriteria()->select('entries.name as name');
        $criteria->where("idFrameElement = {$this->getId()}");
        Base::entryLanguage($criteria);
        return $criteria->asQuery()->fields('name');
    }

    public function getFrame() {
        $vf = new ViewFrame();
        $criteria = $vf->getCriteria()->select('idFrame')->where("fes.idFrameElement = {$this->getId()}");
        return Frame::create($criteria->asQuery()->getResult()[0]['idFrame']);
    }

    public function getById($id)
    {
        parent::getById($id);
        $coreType = new TypeInstance();
        $criteria = $coreType->getCriteria()->select('idTypeInstance');
        Base::relation($criteria, 'FrameElement', 'TypeInstance', 'rel_hastype');
        $criteria->where("frameelement.idFrameElement = '{$id}'");
        $result = $criteria->asQuery()->getResult();
        $this->setIdCoreType($result[0]['idTypeInstance']);
        $criteria = $this->getCriteria()->select('frame.idFrame');
        Base::relation($criteria, 'FrameElement', 'Frame', 'rel_elementof');
        $criteria->where("idFrameElement = '{$id}'");
        $result = $criteria->asQuery()->getResult();
        $this->setIdFrame($result[0]['idFrame']);
    }
    
    public function getByEntry($entry)
    {
        $criteria = $this->getCriteria()->select('*');
        $criteria->where("entry = '{$entry}'");
        $this->retrieveFromCriteria($criteria);
    }    

    public function getStylesByFrame($idFrame)
    {
        $criteria = $this->getCriteria()->select('idFrameElement, entry, entries.name as name, color.rgbFg, color.rgbBg');
        Base::entryLanguage($criteria);
        Base::relation($criteria, 'FrameElement', 'Frame', 'rel_elementof');
        $criteria->where("idFrame = '{$idFrame}'");
        $result = $criteria->asQuery()->getResult();
        $styles = [];
        foreach ($result as $fe) {
            $name = strtolower($fe['name']);//
            $styles[$name] = ['fe' => $name, 'entry' => $fe['entry'], 'rgbFg' => $fe['rgbFg'], 'rgbBg' => $fe['rgbBg']];
        }
        return $styles;
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('idFrameElement');
        if ($filter->idFrameElement) {
            $criteria->where("idFrameElement LIKE '{$filter->idFrameElement}%'");
        }
        if ($filter->idFrame) {
            Base::relation($criteria, 'FrameElement', 'Frame', 'rel_elementof');
            $criteria->where("frame.idFrame = {$filter->idFrame}");
        }        
        return $criteria;
    }

    public function listForLookup($idFrame = '')
    {
        $criteria = $this->getCriteria()->select('idFrameElement,entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        if ($idFrame) {
            Base::relation($criteria, 'FrameElement', 'Frame', 'rel_elementof');
            $criteria->where("frame.idFrame = {$idFrame}");
        }
        return $criteria;
    }

    public function listForReport($idFrame = '')
    {
        $criteria = $this->getCriteria()->select('idFrameElement,entry,entries.name as name, entries.description as description, entries.nick as nick, typeinstance.entry as coreType')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        Base::relation($criteria, 'FrameElement', 'TypeInstance', 'rel_hastype');
        if ($idFrame) {
            Base::relation($criteria, 'FrameElement', 'Frame', 'rel_elementof');
            $criteria->where("frame.idFrame = {$idFrame}");
        }
        return $criteria;
    }

    public function listForEditor($idEntityFrame)
    {
        $criteria = $this->getCriteria()->select('idEntity,entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        Base::relation($criteria, 'FrameElement', 'Frame', 'rel_elementof');
        $criteria->where("frame.idEntity = {$idEntityFrame}");
        return $criteria;
    }

    public function listCoreForEditor($idEntityFrame)
    {
        $criteria = $this->getCriteria()->select('idEntity,entries.name as name')->orderBy('entries.name');
        Base::entryLanguage($criteria);
        Base::relation($criteria, 'FrameElement', 'TypeInstance', 'rel_hastype');
        Base::relation($criteria, 'FrameElement', 'Frame', 'rel_elementof');
        $criteria->where("typeinstance.entry = 'cty_core'");
        $criteria->where("frame.idEntity = {$idEntityFrame}");
        return $criteria;
    }

    public function listFE2SemanticType($idFrameElement = '')
    {
        $idLanguage = \Manager::getSession()->idLanguage;
        $id = ($idFrameElement ?: $this->getId());
        $criteria = $this->getCriteria()->select('SemanticType.idEntity, SemanticType.entry, Entry.name');
        Base::relation($criteria, 'FrameElement fe', 'SemanticType', 'rel_hassemtype');
        $criteria->join("SemanticType","Entry","SemanticType.entry = Entry.entry");
        $criteria->where("Entry.idLanguage = {$idLanguage}");
        $criteria->where("fe.idFrameElement = {$id}");
        return $criteria;
    }

    public function listFE2FERelation($idFrameElement = '', $relationType = 'rel_coreset')
    {
        $id = ($idFrameElement ?: $this->getId());
        $criteria = $this->getCriteria()->select('fe2.idEntity, fe2.entry');
        Base::relation($criteria, 'FrameElement fe1', 'FrameElement fe2', $relationType);
        $criteria->where("fe1.idFrameElement = {$id}");
        return $criteria;
    }

    public function listCoreSet($idFrameElement = '')
    {
        return $this->listFE2FERelation($idFrameElement, 'rel_coreset');
    }

    public function listExcludes($idFrameElement = '')
    {
        return $this->listFE2FERelation($idFrameElement, 'rel_excludes');
    }

    public function listRequires($idFrameElement = '')
    {
        return $this->listFE2FERelation($idFrameElement, 'rel_requires');
    }

    public function listConstraints()
    {
        $constraints = [];
        $constraint = new ViewConstraint();
        $frameConstraints = $constraint->getByIdConstrained($this->getIdEntity());
        foreach ($frameConstraints as $frameConstraint) {
            $constraints[] = $frameConstraint;
        }
        $metonymyConstraints = $constraint->listFEMetonymyConstraints($this->getIdEntity());
        foreach ($metonymyConstraints as $metonymyConstraint) {
            $constraints[] = $metonymyConstraint;
        }
        return $constraints;
    }

    public function listForExport($idFrame)
    {
        $criteria = $this->getCriteria()->select('idFrameElement, entry, active, idEntity, idColor, typeinstance.entry as coreType')->orderBy('entry');
        Base::relation($criteria, 'FrameElement', 'Frame', 'rel_elementof');
        Base::relation($criteria, 'FrameElement', 'TypeInstance', 'rel_hastype');
        $criteria->where("frame.idFrame = {$idFrame}");
        return $criteria;
    }    

    public function save($data)
    {
        $transaction = $this->beginTransaction();
        try {
            if ($this->isPersistent()) {
                $coreType = new TypeInstance($this->getIdCoreType());
                Base::updateEntityRelation($this->getIdEntity(), 'rel_hastype', $coreType->getIdEntity());
                $this->setActive(true);
                $criteria = $this->getCriteria()->select('fe1.idFrameElement');
                Base::relation($criteria, 'FrameElement fe1', 'FrameElement fe2', 'rel_hastemplate');
                $criteria->where("fe2.idEntity = {$this->getIdEntity()}");
                $fes = $criteria->asQuery()->getResult();
                foreach($fes as $fe) {
                    $feTemplated = new FrameElement($fe['idFrameElement']);
                    $feTemplated->setIdColor($this->getIdColor());
                    $feTemplated->save();
                }
            } else {
                if ($data->idFrame) {
                    $schema = new Frame($data->idFrame);
                } else if ($data->idTemplate) {
                    $schema = new Template($data->idTemplate);
                }
                $entity = new Entity();
                $entity->setAlias($this->getEntry());
                $entity->setType('FE');
                $entity->save();
                $entry = new Entry();
                $entry->newEntry($this->getEntry());
                Base::createEntityRelation($entity->getId(), 'rel_elementof', $schema->getIdEntity());
                $coreType = new TypeInstance($data->idCoreType);
                Base::createEntityRelation($entity->getId(), 'rel_hastype', $coreType->getIdEntity());
                $this->setIdEntity($entity->getId());
                $this->setActive(true);
            }
            Base::entityTimelineSave($this->getIdEntity());
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

    public function safeDelete() {
        $fe = new ViewFrameElement();
        $count = count($fe->relations($this->getId(), '', 'rgp_frame_relations')->asQuery()->getResult());
        if ($count > 0) {
            throw new \Exception("This FrameElement has Relations! Removal canceled.");
        } else {
            if ($fe->hasAnnotations($this->getId())) {
                throw new \Exception("This FrameElement has Annotations! Removal canceled.");
            } else {
                $this->delete();
            }
        }
    }
    
    public function delete() {
        $transaction = $this->beginTransaction();
        try {
            $idEntity = $this->getIdEntity();
            // remove entry
            $entry = new Entry();
            $entry->deleteEntry($this->getEntry());
            // remove fe-relations
            Base::deleteAllEntityRelation($idEntity);
            // remove labels
            $label = new Label();
            $label->deleteByIdLabelType($idEntity);
            Base::entityTimelineDelete($this->getIdEntity());
            // remove this fe
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
    
    public function createFromData($fe)
    {
        $this->setPersistent(false);
        $this->setEntry($fe->entry);
        $this->setActive($fe->active);
        $this->setIdEntity($fe->idEntity);
        $this->setIdColor($fe->idColor);
        $coreType = new TypeInstance();
        $idCoreType = $coreType->getIdCoreTypeByEntry($fe->coreType);
        $coreType->getById($idCoreType);
        Base::createEntityRelation($fe->idEntity, 'rel_hastype', $coreType->getIdEntity());
        parent::save();
    }    
    
    public function createRelationsFromData($fe)
    {
        if ($fe->idFrame) {
            $frame = new Frame($fe->idFrame);
            Base::createEntityRelation($this->getIdEntity(), 'rel_elementof', $frame->getIdEntity());
        }
        $feRelated = new FrameElement();
        if ($fe->coreset) {
            foreach($fe->coreset as $coreset) {
                $feRelated->getByEntry($coreset->entry);
                Base::createEntityRelation($this->getIdEntity(), 'rel_coreset', $feRelated->getIdEntity());
            }
        }
        if ($fe->excludes) {
            foreach($fe->excludes as $excludes) {
                $feRelated->getByEntry($excludes->entry);
                Base::createEntityRelation($this->getIdEntity(), 'rel_excludes', $feRelated->getIdEntity());
            }
        }
        if ($fe->requires) {
            foreach($fe->requires as $requires) {
                $feRelated->getByEntry($requires->entry);
                Base::createEntityRelation($this->getIdEntity(), 'rel_requires', $feRelated->getIdEntity());
            }
        }
    }

}

