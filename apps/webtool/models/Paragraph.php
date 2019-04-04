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

class Paragraph extends map\ParagraphMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'documentOrder' => array('notnull'),
                'idDocument' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdParagraph();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idParagraph');
        if ($filter->idParagraph){
            $criteria->where("idParagraph LIKE '{$filter->idParagraph}%'");
        }
        return $criteria;
    }
}

?>