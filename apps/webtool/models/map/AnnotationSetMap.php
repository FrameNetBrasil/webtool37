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
                'idSentence' => array('column' => 'idSentence','type' => 'integer'),
                'idAnnotationStatus' => array('column' => 'idAnnotationStatus','type' => 'integer'),
                'idEntityLU' => array('column' => 'idEntityRelated','type' => 'integer'),
                'idEntityCxn' => array('column' => 'idEntityRelated','type' => 'integer'),
                'idEntityRelated' => array('column' => 'idEntityRelated','type' => 'integer'),
            ),
            'associations' => array(
                'lu' => array('toClass' => 'fnbr\models\LU', 'cardinality' => 'oneToOne' , 'keys' => 'idEntityLU:idEntity'),
                'cxn' => array('toClass' => 'fnbr\models\Construction', 'cardinality' => 'oneToOne' , 'keys' => 'idEntityCxn:idEntity'),
                'sentence' => array('toClass' => 'fnbr\models\Sentence', 'cardinality' => 'oneToOne' , 'keys' => 'idSentence:idSentence'),
                'annotationStatus' => array('toClass' => 'fnbr\models\TypeInstance', 'cardinality' => 'oneToOne' , 'keys' => 'idAnnotationStatus:idTypeInstance'), 
                'layers' => array('toClass' => 'fnbr\models\Layer', 'cardinality' => 'oneToMany' , 'keys' => 'idAnnotationSet:idAnnotationSet'), 
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
     * @var integer 
     */
//    protected $idSubCorpus;
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

    protected $idEntityLU;
    protected $idEntityCxn;
    protected $idEntityRelated;

    /**
     * Associations
     */
    protected $lu;
    protected $cxn;
//    protected $subcorpus;
    protected $sentence;
    protected $annotationStatus;
    protected $layers;


    /**
     * Getters/Setters
     */
    public function getIdAnnotationSet() {
        return $this->idAnnotationSet;
    }

    public function setIdAnnotationSet($value) {
        $this->idAnnotationSet = $value;
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
    public function getIdEntityLU() {
        return $this->idEntityRelated;
    }

    public function setIdEntityLU($value) {
        $this->idEntityRelated = $value;
    }
    public function getIdEntityCxn() {
        return $this->idEntityRelated;
    }

    public function setIdEntityCxn($value) {
        $this->idEntityRelated = $value;
    }
    public function getIdEntityRelated() {
        return $this->idEntityRelated;
    }

    public function setIdEntityRelated($value) {
        $this->idEntityRelated = $value;
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

    public function getLU() {
        if (is_null($this->lu)){
            $this->retrieveAssociation("lu");
        }
        return  $this->lu;
    }
    public function setLU($value) {
        $this->lu = $value;
    }
    public function getAssociationLU() {
        $this->retrieveAssociation("lu");
    }

    public function getCxn() {
        if (is_null($this->cxn)){
            $this->retrieveAssociation("cxn");
        }
        return  $this->cxn;
    }
    public function setCxn($value) {
        $this->cxn = $value;
    }
    public function getAssociationCxn() {
        $this->retrieveAssociation("cxn");
    }


}
