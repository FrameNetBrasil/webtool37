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
                'idAnnotationSetMM' => array('column' => 'idAnnotationSetMM','type' => 'integer'),
                'idFrameElement' => array('column' => 'idFrameElement','type' => 'integer'),
            ),
            'associations' => array(
                'annotationsetmm' => array('toClass' => 'fnbr\models\AnnotationSetMM', 'cardinality' => 'oneToOne' , 'keys' => 'idAnnotationSetMM:idAnnotationSetMM'),
                'frameelement' => array('toClass' => 'fnbr\models\FrameElement', 'cardinality' => 'oneToOne' , 'keys' => 'idFrameElement:idFrameElement'),
                'objectframes' => array('toClass' => 'fnbr\models\ObjectFrameMM', 'cardinality' => 'oneToMany' , 'keys' => 'idObjectMM:idObjectMM'),
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
     * @var string
     */
    protected $idAnnotationSetMM;
    /**
     *
     * @var string
     */
    protected $idFrameElement;

    /**
     * Associations
     */
    protected $annotationSetMM;
    protected $frameElement;


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

    public function getIdAnnotationSetMM() {
        return $this->idAnnotationSetMM;
    }

    public function setIdAnnotationSetMM($value) {
        $this->idAnnotationSetMM = $value;
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
    public function getAnnotationSetMM() {
        if (is_null($this->annotationSetMM)){
            $this->retrieveAssociation("anotattionsetmm");
        }
        return  $this->annotationSetMM;
    }
    /**
     *
     * @param Association $value
     */
    public function setAnnotationSetMM($value) {
        $this->annotationSetMM = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationAnnotationSetMM() {
        $this->retrieveAssociation("annotationsetmm");
    }
    /**
     *
     * @return Association
     */
    public function getFrameElement() {
        if (is_null($this->frameElement)){
            $this->retrieveAssociation("frameelement");
        }
        return  $this->frameElement;
    }
    /**
     *
     * @param Association $value
     */
    public function setFrameElement($value) {
        $this->frameElement = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationFrameElement() {
        $this->retrieveAssociation("frameelement");
    }

}
// end - wizard