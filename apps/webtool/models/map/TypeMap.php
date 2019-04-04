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

class TypeMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'type',
            'attributes' => array(
                'idType' => array('column' => 'idType','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'entry' => array('column' => 'entry','type' => 'string'),
            ),
            'associations' => array(
                'typeinstances' => array('toClass' => 'fnbr\models\TypeInstance', 'cardinality' => 'oneToMany' , 'keys' => 'idType:idType'), 
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany' , 'keys' => 'entry:entry'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idType;
    /**
     * 
     * @var string 
     */
    protected $entry;

    /**
     * Associations
     */
    protected $typeinstances;
    protected $entries;
    

    /**
     * Getters/Setters
     */
    public function getIdType() {
        return $this->idType;
    }

    public function setIdType($value) {
        $this->idType = $value;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setEntry($value) {
        $this->entry = $value;
    }
    /**
     *
     * @return Association
     */
    public function getTypeinstances() {
        if (is_null($this->typeinstances)){
            $this->retrieveAssociation("typeinstances");
        }
        return  $this->typeinstances;
    }
    /**
     *
     * @param Association $value
     */
    public function setTypeinstances($value) {
        $this->typeinstances = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationTypeinstances() {
        $this->retrieveAssociation("typeinstances");
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