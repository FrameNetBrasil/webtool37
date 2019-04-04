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

class ConstructionElementMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'constructionelement',
            'attributes' => array(
                'idConstructionElement' => array('column' => 'idConstructionElement','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'optional' => array('column' => 'optional','type' => 'boolean'),
                'head' => array('column' => 'head','type' => 'boolean'),
                'multiple' => array('column' => 'multiple','type' => 'boolean'),
                'active' => array('column' => 'active','type' => 'boolean'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
                'idColor' => array('column' => 'idColor','type' => 'integer'),
            ),
            'associations' => array(
                'entity' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity:idEntity'), 
                'color' => array('toClass' => 'fnbr\models\Color', 'cardinality' => 'oneToOne' , 'keys' => 'idColor:idColor'),
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany' , 'keys' => 'entry:entry'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idConstructionElement;
    /**
     * 
     * @var string 
     */
    protected $entry;
    /**
     * 
     * @var integer 
     */
    protected $active;
    /**
     *
     * @var boolean
     */
    protected $optional;
    /**
     *
     * @var boolean
     */
    protected $head;
    /**
     *
     * @var boolean
     */
    protected $multiple;
    /**
     * 
     * @var integer 
     */
    protected $idEntity;
    /**
     * 
     * @var integer 
     */
    protected $idColor;

    /**
     * Associations
     */
    protected $entity;
    protected $color;
    protected $entries;
    

    /**
     * Getters/Setters
     */
    public function getIdConstructionElement() {
        return $this->idConstructionElement;
    }

    public function setIdConstructionElement($value) {
        $this->idConstructionElement = $value;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setEntry($value) {
        $this->entry = $value;
    }

    public function getActive() {
        return $this->active;
    }

    public function setActive($value) {
        $this->active = $value;
    }

    public function getOptional() {
        return $this->optional;
    }

    public function setOptional($value) {
        $this->optional = ($value ? 1 : 0);
    }

    public function getHead() {
        return $this->head;
    }

    public function setHead($value) {
        $this->head = ($value ? 1 : 0);
    }

    public function getMultiple() {
        return $this->multiple;
    }

    public function setMultiple($value) {
        $this->multiple = ($value ? 1 : 0);
    }

    public function getIdEntity() {
        return $this->idEntity;
    }

    public function setIdEntity($value) {
        $this->idEntity = $value;
    }

    public function getIdColor() {
        return $this->idColor;
    }

    public function setIdColor($value) {
        $this->idColor = $value;
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