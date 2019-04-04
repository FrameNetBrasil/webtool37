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

class SubCorpus extends map\SubCorpusMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'entry' => array('notnull'),
                'rank' => array('notnull'),
                'idEntity' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription()
    {
        return $this->getIdSubCorpus();
    }

    public function getIdLU()
    {
        $criteria = $this->getCriteria()->select('lu.idLU');
        Base::relation($criteria, 'LU', 'SubCorpus', 'rel_hassubcorpus');
        $criteria->where("idSubCorpus = {$this->getId()}");
        $result = $criteria->asQuery()->getResult();
        return $result[0]['idLU'];
    }
    
    public function getIdCxn()
    {
        $criteria = $this->getCriteria()->select('construction.idConstruction');
        Base::relation($criteria, 'Construction', 'SubCorpus', 'rel_hassubcorpus');
        $criteria->where("idSubCorpus = {$this->getId()}");
        $result = $criteria->asQuery()->getResult();
        return $result[0]['idConstruction'];
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('idSubCorpus');
        if ($filter->idSubCorpus) {
            $criteria->where("idSubCorpus LIKE '{$filter->idSubCorpus}%'");
        }
        return $criteria;
    }



    public function listByCxn($idCxn, $idLanguage = '')
    {
        $criteria = $this->getCriteria()->select('idSubCorpus, name, count(annotationsets.idAnnotationSet) as quant');
        Base::relation($criteria, 'Construction', 'SubCorpus', 'rel_hassubcorpus');
        $criteria->where("construction.idConstruction = {$idCxn}");
        $criteria->groupBy('idSubCorpus,name');
        $criteria->orderBy('name');
        return $criteria;
    }

    public function addSubCorpusLU($data)
    {
        $transaction = $this->beginTransaction();
        try {
            $scName = $data->subCorpus;
            $alias = 'sco_' . strtolower($scName);
            $entity = Base::createEntity('SC', $alias);
            $this->setName($scName);
            $this->setRank(0);
            $this->setIdEntity($entity->getId());
            $this->save();
            $lu = LU::create($data->idLU);
            Base::createEntityRelation($lu->getIdEntity(), 'rel_hassubcorpus', $this->getIdEntity());
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function addSubCorpusCxn($data)
    {
        $transaction = $this->beginTransaction();
        try {
            $scName = $data->subCorpus;
            $alias = 'sco_' . strtolower($scName);
            $entity = Base::createEntity('SC', $alias);
            $this->setName($scName);
            $this->setRank(0);
            $this->setIdEntity($entity->getId());
            $this->save();
            $cxn = Construction::create($data->idConstruction);
            Base::createEntityRelation($cxn->getIdEntity(), 'rel_hassubcorpus', $this->getIdEntity());
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function createAnnotation($data)
    {
        $annotationSet = new AnnotationSet();
        $annotationSet->setIdSentence($data->idSentence);
        $annotationSet->setIdSubCorpus($this->getId());
        $annotationSet->setIdAnnotationStatus('ast_unann');
        $annotationSet->save();
        $lu = LU::create($data->idLU);
        $annotationSet->createLayersForLU($lu, $data);
    }

    public function createAnnotationCxn($data)
    {
        $annotationSet = new AnnotationSet();
        $annotationSet->setIdSentence($data->idSentence);
        $annotationSet->setIdSubCorpus($this->getId());
        $annotationSet->setIdAnnotationStatus('ast_unann');
        $annotationSet->save();
        $cxn = Construction::create($data->idConstruction);
        $annotationSet->createLayersForCxn($cxn, $data);
    }
    
    public function addManualSubCorpusLU($data)
    {
        $transaction = $this->beginTransaction();
        try {
            // verifica se a LU já tem um SubCorpus manually-added
            $criteria = $this->getCriteria()->select('min(idSubCorpus) as idSubCorpusManual');
            Base::relation($criteria, 'LU lu', 'SubCorpus', 'rel_hassubcorpus');
            $criteria->where("lu.idLU = {$data->idLU}");
            $criteria->where("entity.alias like 'sco_manually-added%'");
            $idSubCorpus = $criteria->asQuery()->getResult()[0]['idSubCorpusManual'];
            $lu = LU::create($data->idLU);
            if ($idSubCorpus) {
                $this->getById($idSubCorpus);
            } else {
                $entity = Base::createEntity('SC', 'sco_manually-added');
                $this->setName('manually-added');
                $this->setRank(0);
                $this->setIdEntity($entity->getId());
                $this->save();
                Base::createEntityRelation($lu->getIdEntity(), 'rel_hassubcorpus', $this->getIdEntity());
            }
            $annotationSet = new AnnotationSet();
            $annotationSet->setIdSubCorpus($this->getId());
            $annotationSet->setIdSentence($data->idSentence);
            $annotationSet->setIdAnnotationStatus('ast_manual');
            $annotationSet->save();
            $annotationSet->createLayersForLU($lu, $data);
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollback();
            mdump($ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }

    public function addManualSubCorpusCxn($data)
    {
        $transaction = $this->beginTransaction();
        try {
            $entity = Base::createEntity('SC', 'sco_manually-added');
            $this->setName('manually-added');
            $this->setRank(0);
            $this->setIdEntity($entity->getId());
            $this->save();
            $cxn = Construction::create($data->idConstruction);
            Base::createEntityRelation($cxn->getIdEntity(), 'rel_hassubcorpus', $this->getIdEntity());
            $annotationSet = new AnnotationSet();
            $annotationSet->setIdSubCorpus($this->getId());
            $annotationSet->setIdSentence($data->idSentence);
            $annotationSet->setIdAnnotationStatus('ast_manual');
            $annotationSet->save();
            $annotationSet->createLayersForCxn($cxn, $data);
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollback();
            throw new \Exception($ex->getMessage());
        }
    }
    
    public function addManualSubCorpusDocument($data)
    {
        $transaction = $this->beginTransaction();
        try {
            $entity = Base::createEntity('SC', 'sco_document-related');
            $this->setName('document-related');
            $this->setRank(0);
            $this->setIdEntity($entity->getId());
            $this->save();
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function hasAnnotationSet() {
        $as = new AnnotationSet();
        $criteria = $as->listBySubCorpus($this->getIdSubCorpus());
        return ($criteria->asQuery()->count() > 0);
    }

    public function save()
    {
        Base::entityTimelineSave($this->getIdEntity());
        parent::save();
    }

    public function delete() {
        $transaction = $this->beginTransaction();
        try {
            Base::entityTimelineDelete($this->getIdEntity());
            Base::deleteEntity2Relation($this->getIdEntity(), 'rel_hassubcorpus');
            parent::delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteForced() {
        $transaction = $this->beginTransaction();
        try {
            $as = new AnnotationSet();
            $as->deleteBySubCorpus($this->getIdSubCorpus());
            $this->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

}
