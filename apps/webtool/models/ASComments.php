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

class ASComments extends map\ASCommentsMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'idAnnotationSet' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdASComments();
    }

    public function getByAnnotationSet($idAnnotationSet)
    {
        $criteria = $this->getCriteria()->select('*');
        $criteria->where("idAnnotationSet = {$idAnnotationSet}");
        $this->retrieveFromCriteria($criteria);
    }

    public function deleteByAnnotationSet($idAnnotationSet)
    {
        $transaction = $this->beginTransaction();
        try {
            $deleteCriteria = $this->getDeleteCriteria()->where("idAnnotationSet = {$idAnnotationSet}");
            $deleteCriteria->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }
}

