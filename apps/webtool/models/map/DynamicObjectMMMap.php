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

class DynamicObjectMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'dynamicobjectmm',
            'attributes' => array(
                'idDynamicObjectMM' => array('column' => 'idDynamicObjectMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
                'startFrame' => array('column' => 'startFrame','type' => 'integer'),
                'endFrame' => array('column' => 'endFrame','type' => 'integer'),
                'startTime' => array('column' => 'startTime','type' => 'float'),
                'endTime' => array('column' => 'endTime','type' => 'float'),
                'status' => array('column' => 'status','type' => 'integer'),
                'origin' => array('column' => 'origin','type' => 'integer'),
                'idDocument' => array('column' => 'idDocument','type' => 'integer'),
                'idFrameElement' => array('column' => 'idFrameElement','type' => 'integer'),
                'idLU' => array('column' => 'idLU','type' => 'integer'),
            ),
            'associations' => array(
                'document' => array('toClass' => 'fnbr\models\Document', 'cardinality' => 'oneToOne' , 'keys' => 'idDocument:idDocument'),
                'dynamicbboxmm' => array('toClass' => 'fnbr\models\DynamicBBoxMM', 'cardinality' => 'oneToMany' , 'keys' => 'idDynamicObjectMM:idDynamicObjectMM'),
                'frameelement' => array('toClass' => 'fnbr\models\FrameElement', 'cardinality' => 'oneToOne' , 'keys' => 'idFrameElement:idFrameElement'),
                'lu' => array('toClass' => 'fnbr\models\LU', 'cardinality' => 'oneToOne' , 'keys' => 'idLU:idLU'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idDynamicObjectMM;
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
    protected $idDocument;
    /**
     *
     * @var int
     */
    protected $idFrameElement;
    /**
     *
     * @var int
     */
    protected $idLU;

    /**
     * Associations
     */
    protected $document;
    protected $dynamicbboxmm;


    /**
     * Getters/Setters
     */
    public function getIdDynamicObjectMM() {
        return $this->idDynamicObjectMM;
    }

    public function setIdDynamicObjectMM($value) {
        $this->idDynamicObjectMM = $value;
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

    public function getIdDocument() {
        return $this->idDocument;
    }

    public function setIdDocument($value) {
        $this->idDocument = $value;
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
    public function getDocument() {
        if (is_null($this->document)){
            $this->retrieveAssociation("document");
        }
        return  $this->document;
    }
    /**
     *
     * @param Association $value
     */
    public function setDocument($value) {
        $this->document = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationDocument() {
        $this->retrieveAssociation("document");
    }
    /**
     *
     * @return Association
     */
    public function getDynamicBBoxMM() {
        if (is_null($this->dynamicbboxmm)){
            $this->retrieveAssociation("dynamicbboxmm");
        }
        return  $this->dynamicbboxmm;
    }
    /**
     *
     * @param Association $value
     */
    public function setDynamicBBoxMM($value) {
        $this->dynamicbboxmm = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationDynamicBBoxMM() {
        $this->retrieveAssociation("dynamicbboxmm");
    }

}
// end - wizard