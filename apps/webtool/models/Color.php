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

class Color extends map\ColorMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'name' => array('notnull'),
                'rgbFg' => array('notnull'),
                'rgbBg' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getName();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('idColor, name, rgbFg, rgbBg')->orderBy('idColor');
        if ($filter->idColor){
            $criteria->where("idColor = {$filter->idColor}");
        }
        return $criteria;
    }
    
    public function listForLookup(){
        $criteria = $this->getCriteria()->select("idColor, name, rgbFg, rgbBg")->orderBy('name');
        return $criteria;
    }
    
}

?>