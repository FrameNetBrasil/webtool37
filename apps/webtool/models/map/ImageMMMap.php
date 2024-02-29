<?php
namespace fnbr\models\map;

class ImageMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'imagemm',
            'attributes' => array(
                'idImageMM' => array('column' => 'idImageMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
                'width' => array('column' => 'width','type' => 'integer'),
                'height' => array('column' => 'height','type' => 'integer'),
                'depth' => array('column' => 'depth','type' => 'integer'),
                'imagePath' => array('column' => 'imagePath','type' => 'string'),
            ),
            'associations' => array(
                'sentencemm' => array('toClass' => 'fnbr\models\SentenceMM', 'cardinality' => 'oneToMany' , 'keys' => 'idImageMM:idImageMM'),
                'objectmm' => array('toClass' => 'fnbr\models\ObjectMM', 'cardinality' => 'oneToMany' , 'keys' => 'idImageMM:idImageMM'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idImageMM;
    /**
     *
     * @var string
     */
    protected $name;
    /**
     * 
     * @var integer 
     */
    protected $width;
    /**
     *
     * @var integer
     */
    protected $height;
    /**
     *
     * @var integer
     */
    protected $depth;
    /**
     *
     * @var string
     */
    protected $imagePath;

    /**
     * Getters/Setters
     */
    public function getIdImageMM() {
        return $this->idImageMM;
    }

    public function setIdImageMM($value) {
        $this->idImageMM = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
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

    public function getDepth() {
        return $this->depth;
    }

    public function setDepth($value) {
        $this->depth = $value;
    }

    public function getImagePath() {
        return $this->imagePath;
    }

    public function setImagePath($value) {
        $this->imagePath = $value;
    }

}
