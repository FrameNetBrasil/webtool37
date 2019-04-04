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

class Sentence extends map\SentenceMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'text' => array('notnull'),
                'paragraphOrder' => array('notnull'),
                'timeline' => array('notnull'),
                'idParagraph' => array('notnull'),
                'idLanguage' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdSentence();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idSentence');
        if ($filter->idSentence){
            $criteria->where("idSentence LIKE '{$filter->idSentence}%'");
        }
        return $criteria;
    }

    public function save() {
        $timeline = 'sen_' . md5($this->getText());
        $this->setTimeLine(Base::newTimeLine($timeline, 'S'));
        parent::save();
    }

}

?>