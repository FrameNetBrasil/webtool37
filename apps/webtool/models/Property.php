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

class Property extends map\PropertyMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'idEntity' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdProperty();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idProperty');
        if ($filter->idProperty){
            $criteria->where("idProperty LIKE '{$filter->idProperty}%'");
        }
        return $criteria;
    }

    public function save()
    {
        Base::entityTimelineSave($this->getIdEntity());
        parent::save();
    }

    public function delete()
    {
        Base::entityTimelineDelete($this->getIdEntity());
        parent::delete();
    }
}

?>