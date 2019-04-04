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

class Layer extends map\LayerMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'rank' => array('notnull'),
                'timeline' => array('notnull'),
                'idAnnotationSet' => array('notnull'),
                'idLayerType' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription()
    {
        return $this->getIdLayer();
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('idLayer');
        if ($filter->idLayer) {
            $criteria->where("idLayer LIKE '{$filter->idLayer}%'");
        }
        return $criteria;
    }

    public function listByAnnotationSet($idAnnotationSet)
    {
        $criteria = $this->getCriteria()->select('*');
        $criteria->where("idAnnotationSet = {$idAnnotationSet}");
        return $criteria;
    }

    public function setTimeline()
    {
        $timeline = 'lyr_' . md5($this->getIdAnnotationSet() . $this->getIdLayerType());
        parent::setTimeLine(Base::newTimeLine($timeline, 'S'));
    }

    public function save()
    {
        $this->setTimeline();
        parent::save();
    }

    public function deleteByAnnotationSet($idAnnotationSet)
    {
        $transaction = $this->beginTransaction();
        try {
            $label = new Label();
            $criteria = $this->listByAnnotationSet($idAnnotationSet);
            $result = $criteria->asQuery()->getResult();
            foreach ($result as $layer) {
                $idLayer = $layer['idLayer'];
                $deleteLabel = $label->getDeleteCriteria()->where("idLayer = {$idLayer}");
                $deleteLabel->delete();
                $deleteCriteria = $this->getDeleteCriteria()->where("idLayer = {$idLayer}");
                $deleteCriteria->delete();
            }
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
            $label = new Label();
            $deleteLabel = $label->getDeleteCriteria()->where("idLayer = {$this->getIdLayer()}");
            $deleteLabel->delete();
            parent::delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

}

