<?php
/**
 * @category   Maestro
 * @package    UFJF
 *  @subpackage fnbr
 * @copyright  Copyright (c) 2003-2013 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version
 * @since
 */

// wizard - code section created by Wizard Module

namespace fnbr\models\map;

class SentenceMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'sentencemm',
            'attributes' => array(
                'idSentenceMM' => array('column' => 'idSentenceMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'startTimestamp' => array('column' => 'startTimestamp','type' => 'string'),
                'endTimestamp' => array('column' => 'endTimestamp','type' => 'string'),
                'idSentence' => array('column' => 'idSentence','type' => 'integer'),
            ),
            'associations' => array(
                'sentence' => array('toClass' => 'fnbr\models\Sentence', 'cardinality' => 'oneToOne' , 'keys' => 'idSentence:idSentence'),
                'annotationmm' => array('toClass' => 'fnbr\models\AnnotationMM', 'cardinality' => 'oneToMany' , 'keys' => 'idSentenceMM:idSentenceMM'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idSentenceMM;
    /**
     * 
     * @var string 
     */
    protected $startTimestamp;
    /**
     * 
     * @var integer 
     */
    protected $endTimestamp;
    /**
     * 
     * @var string 
     */
    protected $idSentence;
    protected $annotationMM;

    /**
     * Associations
     */
    protected $sentence;


    /**
     * Getters/Setters
     */
    public function getIdSentenceMM() {
        return $this->idSentenceMM;
    }

    public function setIdSentenceMM($value) {
        $this->idSentenceMM = $value;
    }

    public function getStartTimestamp() {
        return $this->startTimestamp;
    }

    public function setStartTimestamp($value) {
        $this->startTimestamp = $value;
    }

    public function getEndTimestamp() {
        return $this->endTimestamp;
    }

    public function setEndTimestamp($value) {
        $this->endTimestamp = $value;
    }

    public function getIdSentence() {
        return $this->idSentence;
    }

    public function setIdSentence($value) {
        $this->idSentence = $value;
    }

    /**
     *
     * @return Association
     */
    public function getSentence() {
        if (is_null($this->sentence)){
            $this->retrieveAssociation("sentence");
        }
        return  $this->sentence;
    }
    /**
     *
     * @param Association $value
     */
    public function setSentence($value) {
        $this->sentence = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationSentence() {
        $this->retrieveAssociation("sentence");
    }
    /**
     *
     * @return Association
     */
    public function getAnnotationMM() {
        if (is_null($this->sentence)){
            $this->retrieveAssociation("annotationmm");
        }
        return  $this->sentence;
    }
    /**
     *
     * @param Association $value
     */
    public function setAnnotation($value) {
        $this->annotationmm = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationAnnotationMM() {
        $this->retrieveAssociation("annotationmm");
    }
}
// end - wizard

