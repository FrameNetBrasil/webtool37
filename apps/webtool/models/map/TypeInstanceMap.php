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

class TypeInstanceMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'typeinstance',
            'attributes' => array(
                'idTypeInstance' => array('column' => 'idTypeInstance','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'info' => array('column' => 'info','type' => 'string'),
                'flag' => array('column' => 'flag','type' => 'integer'),
                'idType' => array('column' => 'idType','type' => 'integer'),
                'idColor' => array('column' => 'idColor','type' => 'integer'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
            ),
            'associations' => array(
                'type' => array('toClass' => 'fnbr\models\Type', 'cardinality' => 'oneToOne' , 'keys' => 'idType:idType'), 
                'color' => array('toClass' => 'fnbr\models\Color', 'cardinality' => 'oneToOne' , 'keys' => 'idColor:idColor'), 
                'entity' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity:idEntity'), 
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany' , 'keys' => 'entry:entry'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idTypeInstance;
    /**
     * 
     * @var string 
     */
    protected $entry;
    /**
     * 
     * @var string 
     */
    protected $info;
    /**
     * 
     * @var integer 
     */
    protected $flag;
    /**
     * 
     * @var integer 
     */
    protected $idType;
    /**
     * 
     * @var integer 
     */
    protected $idColor;
    /**
     * 
     * @var integer 
     */
    protected $idEntity;

    /**
     * Associations
     */
    protected $type;
    protected $color;
    protected $entity;
    protected $entries;
    

    /**
     * Getters/Setters
     */
    public function getIdTypeInstance() {
        return $this->idTypeInstance;
    }

    public function setIdTypeInstance($value) {
        $this->idTypeInstance = $value;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setEntry($value) {
        $this->entry = $value;
    }

    public function getInfo() {
        return $this->info;
    }

    public function setInfo($value) {
        $this->info = $value;
    }

    public function getFlag() {
        return $this->flag;
    }

    public function setFlag($value) {
        $this->flag = $value;
    }

    public function getIdType() {
        return $this->idType;
    }

    public function setIdType($value) {
        $this->idType = $value;
    }

    public function getIdColor() {
        return $this->idColor;
    }

    public function setIdColor($value) {
        $this->idColor = $value;
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
    public function getType() {
        if (is_null($this->type)){
            $this->retrieveAssociation("type");
        }
        return  $this->type;
    }
    /**
     *
     * @param Association $value
     */
    public function setType($value) {
        $this->type = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationType() {
        $this->retrieveAssociation("type");
    }
    /**
     *
     * @return Association
     */
    public function getColor() {
        if (is_null($this->color)){
            $this->retrieveAssociation("color");
        }
        return  $this->color;
    }
    /**
     *
     * @param Association $value
     */
    public function setColor($value) {
        $this->color = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationColor() {
        $this->retrieveAssociation("color");
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
    /**
     *
     * @return Association
     */
    public function getEntries() {
        if (is_null($this->entries)){
            $this->retrieveAssociation("entries");
        }
        return  $this->entries;
    }
    /**
     *
     * @param Association $value
     */
    public function setEntries($value) {
        $this->entries = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationEntries() {
        $this->retrieveAssociation("entries");
    }

    

}
// end - wizard

?>