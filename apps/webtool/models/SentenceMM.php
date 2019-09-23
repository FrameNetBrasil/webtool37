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

class SentenceMM extends map\SentenceMMMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
            ),
            'converters' => array()
        );
    }
    
    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idSentenceMM');
        if ($filter->idSentence){
            $criteria->where("idSentenceMM LIKE '{$filter->idSentenceMM}%'");
        }
        return $criteria;
    }

}