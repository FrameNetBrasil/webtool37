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

    public function listByDocument($idDocument){
        $criteria = $this->getCriteria()->select('*')->orderBy('idSentence');
        $criteria->where('paragraph.document.idDocument','=', $idDocument);
        return $criteria;
    }

    public function countByDocument($idDocument){
        $criteria = $this->getCriteria()->select('count(*) as n')->orderBy('idSentence');
        $criteria->where('paragraph.document.idDocument','=', $idDocument);
        $result = $criteria->asQuery()->getResult();
        return $result[0]['n'];
    }

    public function save() {
        $timeline = 'sen_' . md5($this->getText());
        $this->setTimeLine(Base::newTimeLine($timeline, 'S'));
        parent::save();
    }

    public function hasAnnotation() {
        return (count($this->getAnnotationsets()) > 0);
    }

}