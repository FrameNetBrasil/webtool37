<?php
namespace fnbr\models\map;

class StaticBBoxMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'staticbboxmm',
            'attributes' => array(
                'idStaticBBoxMM' => array('column' => 'idStaticBBoxMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'x' => array('column' => 'x','type' => 'integer'),
                'y' => array('column' => 'y','type' => 'integer'),
                'width' => array('column' => 'width','type' => 'integer'),
                'height' => array('column' => 'height','type' => 'integer'),
                'idStaticObjectMM' => array('column' => 'idStaticObjectMM','type' => 'integer'),
            ),
            'associations' => array(
                'staticobjectmm' => array('toClass' => 'fnbr\models\StaticObjectMM', 'cardinality' => 'oneToOne' , 'keys' => 'idStaticObjectMM:idStaticObjectMM'),
            )
        );
    }
    
    protected $idStaticBBoxMM;
    protected $x;
    protected $y;
    protected $width;
    protected $height;
    protected $idStaticObjectMM;

    /**
     * Associations
     */
    protected $staticObjectMM;


    /**
     * Getters/Setters
     */
    public function getIdStaticBBoxMM() {
        return $this->idStaticBBoxMM;
    }

    public function setIdStaticBBoxMM($value) {
        $this->idStaticBBoxMM = $value;
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

    public function getIdStaticObjectMM() {
        return $this->idStaticObjectMM;
    }

    public function setIdStaticObjectMM($value) {
        $this->idStaticObjectMM = $value;
    }

    /**
     *
     * @return Association
     */
    public function getStaticObjectMM() {
        if (is_null($this->staticObjectMM)){
            $this->retrieveAssociation("staticobjectmm");
        }
        return  $this->staticObjectMM;
    }
    /**
     *
     * @param Association $value
     */
    public function setStaticObjectMM($value) {
        $this->staticObjectMM = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationStaticObjectSetMM() {
        $this->retrieveAssociation("staticobjectmm");
    }

}
