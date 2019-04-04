<?php
namespace fnbr\models\map;

class ConstraintInstanceMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'constraintInstance',
            'attributes' => array(
                'idConstraintInstance' => array('column' => 'idConstraintInstance','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'idConstraintType' => array('column' => 'idConstraintType','type' => 'integer'),
                'idConstraint' => array('column' => 'idConstraint','type' => 'integer'),
                'idConstrained' => array('column' => 'idConstrained','type' => 'integer'),
                'idConstrainedBy' => array('column' => 'idConstrainedBy','type' => 'integer'),
            ),
            'associations' => array(
                'constrainttype' => array('toClass' => 'fnbr\models\ConstraintType', 'cardinality' => 'oneToOne' , 'keys' => 'idConstraintType:idConstraintType'),
                'entityConstraint' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idConstraint:idEntity'),
                'entityConstrained' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idConstrained:idEntity'),
                'entityConstrainedBy' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idConstrainedBy:idEntity'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idConstraintInstance;
    /**
     * 
     * @var integer 
     */
    protected $idConstraintType;
    /**
     * 
     * @var integer 
     */
    protected $idConstraint;
    /**
     * 
     * @var integer 
     */
    protected $idConstrained;
    /**
     *
     * @var integer
     */
    protected $idConstrainedBy;

    /**
     * Associations
     */
    protected $constraittype;
    protected $entityConstraint;
    protected $entityConstrained;
    protected $entityConstrainedBy;


    /**
     * Getters/Setters
     */
    public function getIdConstraintInstance() {
        return $this->idConstraintInstance;
    }

    public function setIdConstraintInstance($value) {
        $this->idConstraintInstance = $value;
    }

    public function getIdConstraintType() {
        return $this->idConstraintType;
    }

    public function setIdConstraintType($value) {
        $this->idConstraintType = $value;
    }

    public function getIdConstraint() {
        return $this->idConstraint;
    }

    public function setIdConstraint($value) {
        $this->idConstraint = $value;
    }

    public function getIdConstrained() {
        return $this->idConstrained;
    }

    public function setIdConstrained($value) {
        $this->idConstrained = $value;
    }

    public function getIdConstrainedBy() {
        return $this->idConstrainedBy;
    }

    public function setIdConstrainedBy($value) {
        $this->idConstrainedBy = $value;
    }
    /**
     *
     * @return Association
     */
    public function getConstraintType() {
        if (is_null($this->constrainttype)){
            $this->retrieveAssociation("constrainttype");
        }
        return  $this->constrainttype;
    }
    /**
     *
     * @param Association $value
     */
    public function setConstraintType($value) {
        $this->constrainttype = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationConstraintType() {
        $this->retrieveAssociation("constrainttype");
    }
    /**
     *
     * @return Association
     */
    public function getEntityConstraint() {
        if (is_null($this->entityConstraint)){
            $this->retrieveAssociation("entityConstraint");
        }
        return  $this->entityConstraint;
    }
    /**
     *
     * @param Association $value
     */
    public function setEntityConstraint($value) {
        $this->entityConstraint = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationEntityConstraint() {
        $this->retrieveAssociation("entityConstraint");
    }
    /**
     *
     * @return Association
     */
    public function getEntityConstrained() {
        if (is_null($this->entityConstrained)){
            $this->retrieveAssociation("entityConstrained");
        }
        return  $this->entityConstrained;
    }
    /**
     *
     * @param Association $value
     */
    public function setEntityConstrained($value) {
        $this->entityConstrained = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationEntityConstrainted() {
        $this->retrieveAssociation("entityConstrained");
    }
    /**
     *
     * @return Association
     */
    public function getEntityConstrainedBy() {
        if (is_null($this->entityConstrainedBy)){
            $this->retrieveAssociation("entityConstrainedBy");
        }
        return  $this->entityConstrainedBy;
    }
    /**
     *
     * @param Association $value
     */
    public function setEntityConstrainedBy($value) {
        $this->entityConstrainedBy = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationEntityConstraintedBy() {
        $this->retrieveAssociation("entityConstrainedBy");
    }
    

}
