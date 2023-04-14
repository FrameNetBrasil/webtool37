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

namespace fnbr\models\map;

class ObjectMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'objectmm',
            'attributes' => array(
                'idObjectMM' => array('column' => 'idObjectMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
                'startFrame' => array('column' => 'startFrame','type' => 'integer'),
                'endFrame' => array('column' => 'endFrame','type' => 'integer'),
                'startTime' => array('column' => 'startTime','type' => 'float'),
                'endTime' => array('column' => 'endTime','type' => 'float'),
                'status' => array('column' => 'status','type' => 'integer'),
                'origin' => array('column' => 'origin','type' => 'integer'),
                'idDocumentMM' => array('column' => 'idDocumentMM','type' => 'integer'),
                'idFrameElement' => array('column' => 'idFrameElement','type' => 'integer'),
                'idFlickr30k' => array('column' => 'idFlickr30k','type' => 'integer'),
                'idImageMM' => array('column' => 'idImageMM','type' => 'integer'),
                'idLemma' => array('column' => 'idLemma','type' => 'integer'),
                'idLU' => array('column' => 'idLU','type' => 'integer'),
            ),
            'associations' => array(
                //'annotationmm' => array('toClass' => 'fnbr\models\AnnotationMM', 'cardinality' => 'oneToMany' , 'keys' => 'idObjectMM:idObjectMM'),
                'imagemm' => array('toClass' => 'fnbr\models\ImageMM', 'cardinality' => 'oneToOne' , 'keys' => 'idImageMM:idImageMM'),
                'documentmm' => array('toClass' => 'fnbr\models\DocumentMM', 'cardinality' => 'oneToOne' , 'keys' => 'idDocumentMM:idDocumentMM'),
                'objectframes' => array('toClass' => 'fnbr\models\ObjectFrameMM', 'cardinality' => 'oneToMany' , 'keys' => 'idObjectMM:idObjectMM'),
                'lemma' => array('toClass' => 'fnbr\models\Lemma', 'cardinality' => 'oneToOne' , 'keys' => 'idLemma:idLemma'),
                'lu' => array('toClass' => 'fnbr\models\LU', 'cardinality' => 'oneToOne' , 'keys' => 'idLU:idLU'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idObjectMM;
    /**
     * 
     * @var string 
     */
    protected $name;
    /**
     *
     * @var string
     */
    protected $startFrame;
    /**
     *
     * @var string
     */
    protected $endFrame;
    /**
     *
     * @var float
     */
    protected $startTime;
    /**
     *
     * @var float
     */
    protected $endTime;
    /**
     *
     * @var int
     */
    protected $origin;
    /**
     *
     * @var int
     */
    protected $idDocumentMM;
    /**
     *
     * @var int
     */
    protected $idFrameElement;
    /**
     *
     * @var int
     */
    protected $idFlickr30k;
    /**
     *
     * @var int
     */
    protected $idImageMM;
    /**
     *
     * @var int
     */
    protected $idLemma;
    /**
     *
     * @var int
     */
    protected $idLU;

    /**
     * Associations
     */
    protected $documentmm;
    protected $annotationmm;
    protected $objectframes;


    /**
     * Getters/Setters
     */
    public function getIdObjectMM() {
        return $this->idObjectMM;
    }

    public function setIdObjectMM($value) {
        $this->idObjectMM = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
    }

    public function getStartFrame() {
        return $this->startFrame;
    }

    public function setStartFrame($value) {
        $this->startFrame = $value;
    }

    public function getEndFrame() {
        return $this->endFrame;
    }

    public function setEndFrame($value) {
        $this->endFrame = $value;
    }

    public function getStartTime() {
        return $this->startTime;
    }

    public function setStartTime($value) {
        $this->startTime = $value;
    }

    public function getEndTime() {
        return $this->endTime;
    }

    public function setEndTime($value) {
        $this->endTime = $value;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($value) {
        $this->status = $value;
    }

    public function getOrigin() {
        return $this->origin;
    }

    public function setOrigin($value) {
        $this->origin = $value;
    }

    public function getIdFrameElement() {
        return $this->idFrameElement;
    }

    public function setIdFrameElement($value) {
        $this->idFrameElement = $value;
    }

    public function getIdFlickr30k() {
        return $this->idFlickr30k;
    }

    public function setIdFlickr30k($value) {
        $this->idFlickr30k = $value;
    }

    public function getIdDocumentMM() {
        return $this->idDocumentMM;
    }

    public function setIdDocumentMM($value) {
        $this->idDocumentMM = $value;
    }

    public function getIdImageMM() {
        return $this->idImageMM;
    }

    public function setIdImageMM($value) {
        $this->idImageMM = $value;
    }

    public function getIdLemma() {
        return $this->idLemma;
    }

    public function setIdLemma($value) {
        $this->idLemma = $value;
    }

    public function getIdLU() {
        return $this->idLU;
    }

    public function setIdLU($value) {
        $this->idLU = $value;
    }

    /**
     *
     * @return Association
     */
    public function getDocumentMM() {
        if (is_null($this->documentmm)){
            $this->retrieveAssociation("documentmm");
        }
        return  $this->documentmm;
    }
    /**
     *
     * @param Association $value
     */
    public function setDocumentMM($value) {
        $this->documentMM = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationDocumentMM() {
        $this->retrieveAssociation("documentmm");
    }
    /**
     *
     * @return Association
     */
    public function getAnnotationMM() {
        if (is_null($this->anotationmm)){
            $this->retrieveAssociation("annotationmm");
        }
        return  $this->annotationmm;
    }
    /**
     *
     * @param Association $value
     */
    public function setAnnotationMM($value) {
        $this->annotationmm = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationAnnotationMM() {
        $this->retrieveAssociation("annotationmm");
    }
    /**
     *
     * @return Association
     */
    public function getObjectFrames() {
        if (is_null($this->objectframes)){
            $this->retrieveAssociation("objectframes");
        }
        return  $this->objectframes;
    }
    /**
     *
     * @param Association $value
     */
    public function setObjectFrames($value) {
        $this->objectframes = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationObjectFrames() {
        $this->retrieveAssociation("objectframes");
    }

}
// end - wizard