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

class UDRelationMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'udrelation',
            'attributes' => array(
                'idUDRelation' => array('column' => 'idUDRelation','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'info' => array('column' => 'info','type' => 'string'),
                'idTypeInstance' => array('column' => 'idTypeInstance','type' => 'integer'),
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
    protected $idUDRelation;
    /**
     * 
     * @var string 
     */
    protected $info;
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

    /**
     * Associations
     */
    protected $typeinstance;
    protected $entity;


    /**
     * Getters/Setters
     */
    public function getIdUDRelation() {
        return $this->idUDRelation;
    }

    public function setIdUDRelation($value) {
        $this->idUDRelation = $value;
    }

    public function getInfo() {
        return $this->info;
    }

    public function setInfo($value) {
        $this->info = $value;
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
}
// end - wizard