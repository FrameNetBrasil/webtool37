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

class AnnotationMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'annotationmm',
            'attributes' => array(
                'idAnnotationMM' => array('column' => 'idAnnotationMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'timeline' => array('column' => 'timeline','type' => 'string'),
                'annotationType' => array('column' => 'annotationType','type' => 'integer'),
                'idSentenceMM' => array('column' => 'idSentenceMM','type' => 'integer'),
                'idFrameElement' => array('column' => 'idFrameElement','type' => 'integer'),
                'idObjectMM' => array('column' => 'idObjectMM','type' => 'integer'),
            ),
            'associations' => array(
                'sentencemm' => array('toClass' => 'fnbr\models\SentenceMM', 'cardinality' => 'oneToOne' , 'keys' => 'idSentenceMM:idSentenceMM'),
                'frameelement' => array('toClass' => 'fnbr\models\ViewFrameElementMM', 'cardinality' => 'oneToOne' , 'keys' => 'idFrameElementMM:idFrameElementMM'),
                'objectmm' => array('toClass' => 'fnbr\models\ObjectMM', 'cardinality' => 'oneToOne' , 'keys' => 'idObjectMM:idObjectMM'),
                'timelines' => array('toClass' => 'fnbr\models\Timeline', 'cardinality' => 'oneToMany' , 'keys' => 'timeline:timeline'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idAnnotationMM;
    /**
     * 
     * @var string 
     */
    protected $timeline;
    /**
     * 
     * @var integer 
     */
    protected $annotationType;
    /**
     * 
     * @var integer 
     */
    protected $idSentenceMM;
    /**
     *
     * @var integer
     */
    protected $idFrameElement;
    /**
     *
     * @var integer
     */
    protected $idObjectMM;

    /**
     * Associations
     */
    protected $sentencemm;
    protected $objectmm;
    protected $framelement;


    /**
     * Getters/Setters
     */
    public function getIdAnnotationMM() {
        return $this->idAnnotationMM;
    }

    public function setIdAnnotationMM($value) {
        $this->idAnnotationMM = $value;
    }

    public function getTimeline() {
        return $this->timeline;
    }

    public function setTimeline($value) {
        $this->timeline = $value;
    }

    public function getAnnotationType() {
        return $this->annotationType;
    }

    public function setAnnotationType($value) {
        $this->annotationType = $value;
    }

    public function getIdSentenceMM() {
        return $this->idSentenceMM;
    }

    public function setIdSentenceMM($value) {
        $this->idSentenceMM = $value;
    }

    public function getIdObjectMM() {
        return $this->idObjectMM;
    }

    public function setIdObjectMM($value) {
        $this->idObjectMM = $value;
    }

    public function getIdFrameElement() {
        return $this->idFrameElement;
    }

    public function setIdFrameElement($value) {
        $this->idFrameElement = $value;
    }
    /**
     *
     * @return Association
     */
    public function getSentenceMM() {
        if (is_null($this->sentencemm)){
            $this->retrieveAssociation("sentencemm");
        }
        return  $this->sentencemm;
    }
    /**
     *
     * @param Association $value
     */
    public function setSentenceMM($value) {
        $this->sentencemm = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationSentenceMM() {
        $this->retrieveAssociation("sentencemm");
    }
    /**
     *
     * @return Association
     */
    public function getTimelines() {
        if (is_null($this->timelines)){
            $this->retrieveAssociation("timelines");
        }
        return  $this->timelines;
    }
    /**
     *
     * @param Association $value
     */
    public function setTimelines($value) {
        $this->timelines = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationTimelines() {
        $this->retrieveAssociation("timelines");
    }
    /**
     *
     * @return Association
     */
    public function getObjectMM() {
        if (is_null($this->objectmm)){
            $this->retrieveAssociation("objectmm");
        }
        return  $this->objectmm;
    }
    /**
     *
     * @param Association $value
     */
    public function setObjectMM($value) {
        $this->objectmm = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationObjectMM() {
        $this->retrieveAssociation("objectmm");
    }
    /**
     *
     * @return Association
     */
    public function getFrameElement() {
        if (is_null($this->objectmm)){
            $this->retrieveAssociation("frameelement");
        }
        return  $this->frameelement;
    }
    /**
     *
     * @param Association $value
     */
    public function setFrameElement($value) {
        $this->frameelement = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationFrameElement() {
        $this->retrieveAssociation("frameelement");
    }

}

