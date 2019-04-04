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

class EntityRelationMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'entityrelation',
            'attributes' => array(
                'idEntityRelation' => array('column' => 'idEntityRelation','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'idRelationType' => array('column' => 'idRelationType','type' => 'integer'),
                'idEntity1' => array('column' => 'idEntity1','type' => 'integer'),
                'idEntity2' => array('column' => 'idEntity2','type' => 'integer'),
                'idEntity3' => array('column' => 'idEntity3','type' => 'integer'),
            ),
            'associations' => array(
                'relationtype' => array('toClass' => 'fnbr\models\RelationType', 'cardinality' => 'oneToOne' , 'keys' => 'idRelationType:idRelationType'), 
                'entity1' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity1:idEntity'), 
                'entity2' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity2:idEntity'), 
                'entity3' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity3:idEntity'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idEntityRelation;
    /**
     * 
     * @var integer 
     */
    protected $idRelationType;
    /**
     * 
     * @var integer 
     */
    protected $idEntity1;
    /**
     * 
     * @var integer 
     */
    protected $idEntity2;

    /**
     * Associations
     */
    protected $relationtype;
    protected $entity1;
    protected $entity2;
    

    /**
     * Getters/Setters
     */
    public function getIdEntityRelation() {
        return $this->idEntityRelation;
    }

    public function setIdEntityRelation($value) {
        $this->idEntityRelation = $value;
    }

    public function getIdRelationType() {
        return $this->idRelationType;
    }

    public function setIdRelationType($value) {
        $this->idRelationType = $value;
    }

    public function getIdEntity1() {
        return $this->idEntity1;
    }

    public function setIdEntity1($value) {
        $this->idEntity1 = $value;
    }

    public function getIdEntity2() {
        return $this->idEntity2;
    }

    public function setIdEntity2($value) {
        $this->idEntity2 = $value;
    }

    public function getIdEntity3() {
        return $this->idEntity3;
    }

    public function setIdEntity3($value) {
        $this->idEntity3 = $value;
    }
    /**
     *
     * @return Association
     */
    public function getRelationtype() {
        if (is_null($this->relationtype)){
            $this->retrieveAssociation("relationtype");
        }
        return  $this->relationtype;
    }
    /**
     *
     * @param Association $value
     */
    public function setRelationtype($value) {
        $this->relationtype = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationRelationtype() {
        $this->retrieveAssociation("relationtype");
    }
    /**
     *
     * @return Association
     */
    public function getEntity1() {
        if (is_null($this->entity1)){
            $this->retrieveAssociation("entity1");
        }
        return  $this->entity1;
    }
    /**
     *
     * @param Association $value
     */
    public function setEntity1($value) {
        $this->entity1 = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationEntity1() {
        $this->retrieveAssociation("entity1");
    }
    /**
     *
     * @return Association
     */
    public function getEntity2() {
        if (is_null($this->entity2)){
            $this->retrieveAssociation("entity2");
        }
        return  $this->entity2;
    }
    /**
     *
     * @param Association $value
     */
    public function setEntity2($value) {
        $this->entity2 = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationEntity2() {
        $this->retrieveAssociation("entity2");
    }
    /**
     *
     * @return Association
     */
    public function getEntity3() {
        if (is_null($this->entity3)){
            $this->retrieveAssociation("entity3");
        }
        return  $this->entity3;
    }
    /**
     *
     * @param Association $value
     */
    public function setEntity3($value) {
        $this->entity3 = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationEntity3() {
        $this->retrieveAssociation("entity3");
    }

    

}
// end - wizard

?>