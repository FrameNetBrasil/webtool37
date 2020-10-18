<?php
namespace fnbr\models\map;

class UDPOSMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'udpos',
            'attributes' => array(
                'idUDPOS' => array('column' => 'idUDPOS','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'POS' => array('column' => 'POS','type' => 'string'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
            ),
            'associations' => array(
                'typeinstance' => array('toClass' => 'fnbr\models\TypeInstance', 'cardinality' => 'oneToOne' , 'keys' => 'idTypeInstance:idTypeInstance'),
                'entity' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity:idEntity'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idUDPOS;
    /**
     * 
     * @var string 
     */
    protected $POS;
    /**
     * 
     * @var string
     */
    protected $entry;
    /**
     * 
     * @var integer 
     */
    protected $idEntity;

    /**
     * Associations
     */
    protected $entity;


    /**
     * Getters/Setters
     */
    public function getIdUDPOS() {
        return $this->idUDPOS;
    }

    public function setIdUDPOS($value) {
        $this->idUDPOS = $value;
    }

    public function getPOS() {
        return $this->POS;
    }

    public function setPOS($value) {
        $this->POS = $value;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setEntry($value) {
        $this->entry = $value;
    }

    public function getIdEntity() {
        return $this->idEntity;
    }

    public function setIdEntity($value) {
        $this->idEntity = $value;
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
}
