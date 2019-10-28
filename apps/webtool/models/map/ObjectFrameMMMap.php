<?php
namespace fnbr\models\map;

class ObjectFrameMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'objectframemm',
            'attributes' => array(
                'idObjectFrameMM' => array('column' => 'idObjectFrameMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'frameNumber' => array('column' => 'frameNumber','type' => 'integer'),
                'x' => array('column' => 'x','type' => 'integer'),
                'y' => array('column' => 'y','type' => 'integer'),
                'width' => array('column' => 'width','type' => 'integer'),
                'height' => array('column' => 'height','type' => 'integer'),
                'blocked' => array('column' => 'blocked','type' => 'integer'),
                'idObjectMM' => array('column' => 'idObjectMM','type' => 'integer'),
            ),
            'associations' => array(
                'objectmm' => array('toClass' => 'fnbr\models\ObjectMM', 'cardinality' => 'oneToOne' , 'keys' => 'idObjectMM:idObjectMM'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idObjectFrameMM;
    /**
     * 
     * @var string 
     */
    protected $frameNumber;
    /**
     *
     * @var string
     */
    protected $x;
    /**
     *
     * @var string
     */
    protected $y;
    /**
     *
     * @var string
     */
    protected $width;
    /**
     *
     * @var string
     */
    protected $height;
    /**
     *
     * @var string
     */
    protected $blocked;
    /**
     *
     * @var string
     */
    protected $status;
    /**
     *
     * @var string
     */
    protected $idObjectMM;

    /**
     * Associations
     */
    protected $objectMM;


    /**
     * Getters/Setters
     */
    public function getIdObjectFrameMM() {
        return $this->idObjectFrameMM;
    }

    public function setIdObjectFrameMM($value) {
        $this->idObjectFrameMM = $value;
    }

    public function getFrameNumber() {
        return $this->frameNumber;
    }

    public function setFrameNumber($value) {
        $this->frameNumber = $value;
    }

    public function getX() {
        return $this->x;
    }

    public function setX($value) {
        $this->x = $value;
    }

    public function getY() {
        return $this->y;
    }

    public function setY($value) {
        $this->y = $value;
    }

    public function getWidth() {
        return $this->width;
    }

    public function setWidth($value) {
        $this->width = $value;
    }

    public function getHeight() {
        return $this->height;
    }

    public function setHeight($value) {
        $this->height = $value;
    }

    public function getBlocked() {
        return $this->blocked;
    }

    public function setBlocked($value) {
        $this->blocked = $value;
    }

    public function getIdObjectMM() {
        return $this->idObjectMM;
    }

    public function setIdObjectMM($value) {
        $this->idObjectMM = $value;
    }

    /**
     *
     * @return Association
     */
    public function getObjectMM() {
        if (is_null($this->objectMM)){
            $this->retrieveAssociation("objectmm");
        }
        return  $this->objectMM;
    }
    /**
     *
     * @param Association $value
     */
    public function setObjectMM($value) {
        $this->objectMM = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationObjectSetMM() {
        $this->retrieveAssociation("objectmm");
    }

}
