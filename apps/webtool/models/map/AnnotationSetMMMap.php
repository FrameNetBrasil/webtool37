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

class AnnotationSetMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'annotationsetmm',
            'attributes' => array(
                'idAnnotationSetMM' => array('column' => 'idAnnotationSetMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'timeline' => array('column' => 'timeline','type' => 'string'),
                'annotationPath' => array('column' => 'annotationPath','type' => 'string'),
                'idSentenceMM' => array('column' => 'idSentenceMM','type' => 'integer'),
                'idAnnotationSet' => array('column' => 'idAnnotationSet','type' => 'integer'),
            ),
            'associations' => array(
                'sentencemm' => array('toClass' => 'fnbr\models\SentenceMM', 'cardinality' => 'oneToOne' , 'keys' => 'idSentenceMM:idSentenceMM'),
                'annotationset' => array('toClass' => 'fnbr\models\AnnotationSet', 'cardinality' => 'oneToOne' , 'keys' => 'idAnnotationSet:idAnnotationSet'),
                'timelines' => array('toClass' => 'fnbr\models\Timeline', 'cardinality' => 'oneToMany' , 'keys' => 'timeline:timeline'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idAnnotationSetMM;
    /**
     * 
     * @var string 
     */
    protected $timeline;
    /**
     * 
     * @var integer 
     */
    protected $annotationPath;
    /**
     * 
     * @var integer 
     */
    protected $idSentenceMM;

    /**
     * Associations
     */
    protected $sentencemm;
    protected $annotationset;


    /**
     * Getters/Setters
     */
    public function getIdAnnotationSetMM() {
        return $this->idAnnotationSetMM;
    }

    public function setIdAnnotationSetMM($value) {
        $this->idAnnotationSetMM = $value;
    }

    public function getTimeline() {
        return $this->timeline;
    }

    public function setTimeline($value) {
        $this->timeline = $value;
    }

    public function getAnnotationPath() {
        return $this->annotationPath;
    }

    public function setAnnotationPath($value) {
        $this->annotationPath = $value;
    }

    public function getIdSentenceMM() {
        return $this->idSentenceMM;
    }

    public function setIdSentenceMM($value) {
        $this->idSentenceMM = $value;
    }

    public function getIdAnnotationSet() {
        return $this->idAnnotationSet;
    }

    public function setIdAnnotationSet($value) {
        $this->idAnnotationSet = $value;
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
    public function getAnnotationSet() {
        if (is_null($this->annotationSet)){
            $this->retrieveAssociation("annotationset");
        }
        return  $this->annotationset;
    }
    /**
     *
     * @param Association $value
     */
    public function setAnnotationSet($value) {
        $this->annotationSet = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationAnnotationSet() {
        $this->retrieveAssociation("annotationset");
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

}
// end - wizard
