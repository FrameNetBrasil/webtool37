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

class AnnotationSetMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'annotationset',
            'attributes' => array(
                'idAnnotationSet' => array('column' => 'idAnnotationSet','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'timeline' => array('column' => 'timeline','type' => 'string'),
                'idSubCorpus' => array('column' => 'idSubCorpus','type' => 'integer'),
                'idSentence' => array('column' => 'idSentence','type' => 'integer'),
                'idAnnotationStatus' => array('column' => 'idAnnotationStatus','type' => 'integer'),
            ),
            'associations' => array(
                'subcorpus' => array('toClass' => 'fnbr\models\SubCorpus', 'cardinality' => 'oneToOne' , 'keys' => 'idSubCorpus:idSubCorpus'), 
                'sentence' => array('toClass' => 'fnbr\models\Sentence', 'cardinality' => 'oneToOne' , 'keys' => 'idSentence:idSentence'), 
                'annotationStatus' => array('toClass' => 'fnbr\models\TypeInstance', 'cardinality' => 'oneToOne' , 'keys' => 'idAnnotationStatus:idTypeInstance'), 
                'layers' => array('toClass' => 'fnbr\models\Layer', 'cardinality' => 'oneToMany' , 'keys' => 'idAnnotationSet:idAnnotationSet'), 
                'timelines' => array('toClass' => 'fnbr\models\Timeline', 'cardinality' => 'oneToMany' , 'keys' => 'timeline:timeline'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idAnnotationSet;
    /**
     * 
     * @var string 
     */
    protected $timeline;
    /**
     * 
     * @var integer 
     */
    protected $idSubCorpus;
    /**
     * 
     * @var integer 
     */
    protected $idSentence;
    /**
     * 
     * @var integer 
     */
    protected $idAnnotationStatus;

    /**
     * Associations
     */
    protected $subcorpus;
    protected $sentence;
    protected $annotationStatus;
    protected $layers;
    protected $timelines;
    

    /**
     * Getters/Setters
     */
    public function getIdAnnotationSet() {
        return $this->idAnnotationSet;
    }

    public function setIdAnnotationSet($value) {
        $this->idAnnotationSet = $value;
    }

    public function getTimeline() {
        return $this->timeline;
    }

    public function setTimeline($value) {
        $this->timeline = $value;
    }

    public function getIdSubCorpus() {
        return $this->idSubCorpus;
    }

    public function setIdSubCorpus($value) {
        $this->idSubCorpus = $value;
    }

    public function getIdSentence() {
        return $this->idSentence;
    }

    public function setIdSentence($value) {
        $this->idSentence = $value;
    }

    public function getIdAnnotationStatus() {
        return $this->idAnnotationStatus;
    }

    public function setIdAnnotationStatus($value) {
        $this->idAnnotationStatus = $value;
    }
    /**
     *
     * @return Association
     */
    public function getSubcorpus() {
        if (is_null($this->subcorpus)){
            $this->retrieveAssociation("subcorpus");
        }
        return  $this->subcorpus;
    }
    /**
     *
     * @param Association $value
     */
    public function setSubcorpus($value) {
        $this->subcorpus = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationSubcorpus() {
        $this->retrieveAssociation("subcorpus");
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
    public function getAnnotationStatus() {
        if (is_null($this->annotationStatus)){
            $this->retrieveAssociation("annotationStatus");
        }
        return  $this->annotationStatus;
    }
    /**
     *
     * @param Association $value
     */
    public function setAnnotationStatus($value) {
        $this->annotationStatus = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationAnnotationStatus() {
        $this->retrieveAssociation("annotationStatus");
    }
    /**
     *
     * @return Association
     */
    public function getLayers() {
        if (is_null($this->layers)){
            $this->retrieveAssociation("layers");
        }
        return  $this->layers;
    }
    /**
     *
     * @param Association $value
     */
    public function setLayers($value) {
        $this->layers = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationLayers() {
        $this->retrieveAssociation("layers");
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

?>