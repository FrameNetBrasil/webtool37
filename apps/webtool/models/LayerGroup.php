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

class LayerGroup extends map\LayerGroupMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'name' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdLayerGroup();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idLayerGroup');
        if ($filter->idLayerGroup){
            $criteria->where("idLayerGroup LIKE '{$filter->idLayerGroup}%'");
        }
        return $criteria;
    }

    public function listAll(){
        $criteria = $this->getCriteria()->select('idLayerGroup, name')->orderBy('name');
        return $criteria;
    }    
}