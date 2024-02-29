<?php
namespace fnbr\models\map;

class DynamicBBoxMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'dynamicbboxmm',
            'attributes' => array(
                'idDynamicBBoxMM' => array('column' => 'idDynamicBBoxMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'frameNumber' => array('column' => 'frameNumber','type' => 'integer'),
                'frameTime' => array('column' => 'frameTime','type' => 'float'),
                'x' => array('column' => 'x','type' => 'integer'),
                'y' => array('column' => 'y','type' => 'integer'),
                'width' => array('column' => 'width','type' => 'integer'),
                'height' => array('column' => 'height','type' => 'integer'),
                'blocked' => array('column' => 'blocked','type' => 'integer'),
                'idDynamicObjectMM' => array('column' => 'idDynamicObjectMM','type' => 'integer'),
            ),
            'associations' => array(
                'dynamicobjectmm' => array('toClass' => 'fnbr\models\DynamicObjectMM', 'cardinality' => 'oneToOne' , 'keys' => 'idDynamicObjectMM:idDynamicObjectMM'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idDynamicBBoxMM;
    /**
     * 
     * @var string 
     */
    protected $frameNumber;
    /**
     *
     * @var float
     */
    protected $frameTime;
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
    protected $idDynamicObjectMM;

    /**
     * Associations
     */
    protected $dynamicObjectMM;


    /**
     * Getters/Setters
     */
    public function getIdDynamicBBoxMM() {
        return $this->idDynamicBBoxMM;
    }

    public function setIdDynamicBBoxMM($value) {
        $this->idDynamicBBoxMM = $value;
    }

    public function getFrameNumber() {
        return $this->frameNumber;
    }

    public function setFrameNumber($value) {
        $this->frameNumber = $value;
    }

    public function getFrameTime() {
        return $this->frameTime;
    }

    public function setFrameTime($value) {
        $this->frameTime = $value;
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

    public function getIdDynamicObjectMM() {
        return $this->idDynamicObjectMM;
    }

    public function setIdDynamicObjectMM($value) {
        $this->idDynamicObjectMM = $value;
    }

    /**
     *
     * @return Association
     */
    public function getDynamicObjectMM() {
        if (is_null($this->dynamicObjectMM)){
            $this->retrieveAssociation("dynamicobjectmm");
        }
        return  $this->dynamicObjectMM;
    }
    /**
     *
     * @param Association $value
     */
    public function setDynamicObjectMM($value) {
        $this->dynamicObjectMM = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationDynamicObjectSetMM() {
        $this->retrieveAssociation("dynamicobjectmm");
    }

}
