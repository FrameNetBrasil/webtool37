<?php

namespace fnbr\models\map;

class ConstraintTypeMap extends \MBusinessModel
{


    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'relationtype',
            'attributes' => array(
                'idConstraintType' => array('column' => 'idRelationType', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'entry' => array('column' => 'entry', 'type' => 'string'),
                'prefix' => array('column' => 'prefix', 'type' => 'string'),
                'typeEntity1' => array('column' => 'nameEntity1', 'type' => 'string'),
                'typeEntity2' => array('column' => 'nameEntity2', 'type' => 'string'),
                'idTypeInstance' => array('column' => 'idTypeInstance', 'type' => 'integer'),
                'idRelationGroup' => array('column' => 'idRelationGroup', 'type' => 'integer'),
            ),
            'associations' => array(
                'typeinstance' => array('toClass' => 'fnbr\models\TypeInstance', 'cardinality' => 'oneToOne', 'keys' => 'idTypeInstance:idTypeInstance'),
                'relationgroup' => array('toClass' => 'fnbr\models\RelationGroup', 'cardinality' => 'oneToOne', 'keys' => 'idRelationGroup:idRelationGroup'),
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany', 'keys' => 'entry:entry'),
            )
        );
    }

    /**
     *
     * @var integer
     */
    protected $idConstraintType;
    /**
     *
     * @var string
     */
    protected $entry;
    /**
     *
     * @var string
     */
    protected $prefix;
    /**
     *
     * @var string
     */
    protected $typeEntity1;
    /**
     *
     * @var string
     */
    protected $typeEntity2;
    /**
     *
     * @var integer
     */
    protected $idTypeInstance;

    /**
     *
     * @var integer
     */
    protected $idRelationGroup;

    /**
     * Associations
     */
    protected $typeinstance;
    protected $entries;


    /**
     * Getters/Setters
     */
    public function getIdConstraintType()
    {
        return $this->idConstraintType;
    }

    public function setIdConstraintType($value)
    {
        $this->idConstraintType = $value;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function setEntry($value)
    {
        $this->entry = $value;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix($value)
    {
        $this->prefix = $value;
    }

    public function getTypeEntity1()
    {
        return $this->typeEntity1;
    }

    public function setTypeEntity1($value)
    {
        $this->typeEntity1 = $value;
    }

    public function getTypeEntity2()
    {
        return $this->typeEntity1;
    }

    public function setTypeEntity2($value)
    {
        $this->typeEntity2 = $value;
    }

    public function getIdTypeInstance()
    {
        return $this->idTypeInstance;
    }

    public function setIdTypeInstance($value)
    {
        $this->idTypeInstance = $value;
    }

    public function setIdRelationGroup($value)
    {
        $this->idRelationGroup = $value;
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
