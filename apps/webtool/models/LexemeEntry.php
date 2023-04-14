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

class LexemeEntry extends map\LexemeEntryMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'lexemeOrder' => array('notnull'),
                'breakBefore' => array('notnull'),
                'headWord' => array('notnull'),
                'idLexeme' => array('notnull'),
                'idLemma' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdLexemeEntry();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idLexemeEntry');
        if ($filter->idLexemeEntry){
            $criteria->where("idLexemeEntry LIKE '{$filter->idLexemeEntry}%'");
        }
        return $criteria;
    }
}

?>