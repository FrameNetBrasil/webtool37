<?php
namespace fnbr\models\map;

class QualiaMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'qualia',
            'attributes' => array(
                'idQualia' => array('column' => 'idQualia','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'info' => array('column' => 'info','type' => 'string'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'idTypeInstance' => array('column' => 'idTypeInstance','type' => 'integer'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
                'idFrame' => array('column' => 'idFrame','type' => 'integer'),
                'idFrameElement1' => array('column' => 'idFrameElement1','type' => 'integer'),
                'idFrameElement2' => array('column' => 'idFrameElement2','type' => 'integer'),
            ),
            'associations' => array(
                'typeinstance' => array('toClass' => 'fnbr\models\TypeInstance', 'cardinality' => 'oneToOne' , 'keys' => 'idTypeInstance:idTypeInstance'),
                'entity' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity:idEntity'),
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany' , 'keys' => 'entry:entry'),
                'frame' => array('toClass' => 'fnbr\models\Frame', 'cardinality' => 'oneToOne' , 'keys' => 'idFrame:idFrame'),
                'frameelement1' => array('toClass' => 'fnbr\models\FrameElement', 'cardinality' => 'oneToOne' , 'keys' => 'idFrameElement1:idFrameElement'),
                'frameelement2' => array('toClass' => 'fnbr\models\FrameElement', 'cardinality' => 'oneToOne' , 'keys' => 'idFrameElement2:idFrameElement'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idQualia;
    /**
     * 
     * @var string 
     */
    protected $info;
    /**
     *
     * @var string
     */
    protected $entry;
    /**
     * 
     * @var integer 
     */
    protected $idTypeInstance;
    /**
     * 
     * @var integer 
     */
    protected $idEntity;

    protected $idFrame;
    protected $idFrameElement1;
    protected $idFrameElement2;
    /**
     * Associations
     */
    protected $typeinstance;
    protected $entity;
    protected $frame;
    protected $frameelement1;
    protected $frameelement2;


    /**
     * Getters/Setters
     */
    public function getIdQualia() {
        return $this->idQualia;
    }

    public function setIdQualia($value) {
        $this->idQualia = $value;
    }

    public function getInfo() {
        return $this->info;
    }

    public function setInfo($value) {
        $this->info = $value;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setEntry($value) {
        $this->entry = $value;
    }

    public function getIdTypeInstance() {
        return $this->idTypeInstance;
    }

    public function setIdTypeInstance($value) {
        $this->idTypeInstance = $value;
    }

    public function getIdEntity() {
        return $this->idEntity;
    }

    public function setIdEntity($value) {
        $this->idEntity = $value;
    }

    public function getIdFrame() {
        return $this->idFrame;
    }

    public function setIdFrame($value) {
        $this->idFrame = $value;
    }

    public function getIdFrameElement1() {
        return $this->idFrameElement1;
    }

    public function setIdFrameElement1($value) {
        $this->idFrameElement1 = $value;
    }

    public function getIdFrameElement2() {
        return $this->idFrameElement2;
    }

    public function setIdFrameElement2($value) {
        $this->idFrameElement2 = $value;
    }
    /**
     *
     * @return Association
     */
    public function getTypeInstance() {
        if (is_null($this->typeinstance)){
            $this->retrieveAssociation("typeinstance");
        }
        return  $this->typeinstance;
    }
    /**
     *
     * @param Association $value
     */
    public function setTypeInstance($value) {
        $this->typeinstance = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationTypeInstance() {
        $this->retrieveAssociation("typeinstance");
    }
    /**
     *
     * @return Association
     */
    public function getEntity() {
        if (is_null($this->entity)){
            $this->retrieveAssociation("entity");
        }
        return  $this->entity;
    }
    /**
     *
     * @param Association $value
     */
    public function setEntity($value) {
        $this->entity = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationEntity() {
        $this->retrieveAssociation("entity");
    }


    public function getFrame() {
        if (is_null($this->frame)){
            $this->retrieveAssociation("frame");
        }
        return  $this->frame;
    }
    public function setFrame($value) {
        $this->frame = $value;
    }
    public function getAssociationFrame() {
        $this->retrieveAssociation("frame");
    }

    public function getFrameElement1() {
        if (is_null($this->frame)){
            $this->retrieveAssociation("frameelement1");
        }
        return  $this->frameelement1;
    }
    public function setFrameElement1($value) {
        $this->frameelement1 = $value;
    }
    public function getAssociationFrameElement1() {
        $this->retrieveAssociation("frameelement1");
    }

    public function getFrameElement2() {
        if (is_null($this->frame)){
            $this->retrieveAssociation("frameelement2");
        }
        return  $this->frameelement2;
    }
    public function setFrameElement2($value) {
        $this->frameelement2 = $value;
    }
    public function getAssociationFrameElement2() {
        $this->retrieveAssociation("frameelement2");
    }

}
