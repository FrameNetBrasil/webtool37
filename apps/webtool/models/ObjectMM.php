<?php
namespace fnbr\models;

class ObjectMM extends map\ObjectMMMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
            ),
            'converters' => array()
        );
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idObjectMM');
        if ($filter->idAnnotationSetMM){
            $criteria->where("idAnnotationSetMM = {$filter->idAnnotationSetMM}");
        }
        return $criteria;
    }

    public function listByAnnotationSetMM($idAnnotationSetMM){
        $criteria = $this->getCriteria()->select('*')->orderBy('idObjectMM');
        $criteria->where("idAnnotationSetMM = {$idAnnotationSetMM}");
        return $criteria;
    }

    public function save($data)
    {
        $transaction = $this->beginTransaction();
        try {
            parent::save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    
    
}
