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

class SemanticTypeMap extends \MBusinessModel
{

    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'semantictype',
            'attributes' => array(
                'idSemanticType' => array('column' => 'idSemanticType', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'entry' => array('column' => 'entry', 'type' => 'string'),
                'idEntity' => array('column' => 'idEntity', 'type' => 'integer'),
                'idDomain' => array('column' => 'idDomain', 'type' => 'integer'),
            ),
            'associations' => array(
                'domain' => array('toClass' => 'fnbr\models\Domain', 'cardinality' => 'oneToOne', 'keys' => 'idDomain:idDomain'),
                'entity' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne', 'keys' => 'idEntity:idEntity'),
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany', 'keys' => 'entry:entry'),
            )
        );
    }

    /**
     * 
     * @var integer 
     */
    protected $idSemanticType;

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
     * 
     * @var integer 
     */
    protected $idDomain;

    /**
     * Associations
     */
    protected $domain;
    protected $entity;
    protected $entries;

    /**
     * Getters/Setters
     */
    public function getIdSemanticType()
    {
        return $this->idSemanticType;
    }

    public function setIdSemanticType($value)
    {
        $this->idSemanticType = $value;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function setEntry($value)
    {
        $this->entry = $value;
    }

    public function getIdEntity()
    {
        return $this->idEntity;
    }

    public function setIdEntity($value)
    {
        $this->idEntity = $value;
    }

    public function getIdDomain()
    {
        return $this->idDomain;
    }

    public function setIdDomain($value)
    {
        $this->idDomain = $value;
    }
    /**
     *
     * @return Association
     */
    public function getDomain() {
        if (is_null($this->domain)){
            $this->retrieveAssociation("domain");
        }
        return  $this->domain;
    }
    /**
     *
     * @param Association $value
     */
    public function setDomain($value) {
        $this->domain = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationDomain() {
        $this->retrieveAssociation("domain");
    }

    /**
     *
     * @return Association
     */
    public function getEntity()
    {
        if (is_null($this->entity)) {
            $this->retrieveAssociation("entity");
        }
        return $this->entity;
    }

    /**
     *
     * @param Association $value
     */
    public function setEntity($value)
    {
        $this->entity = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAssociationEntity()
    {
        $this->retrieveAssociation("entity");
    }

    /**
     *
     * @return Association
     */
    public function getEntries()
    {
        if (is_null($this->entries)) {
            $this->retrieveAssociation("entries");
        }
        return $this->entries;
    }

    /**
     *
     * @param Association $value
     */
    public function setEntries($value)
    {
        $this->entries = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAssociationEntries()
    {
        $this->retrieveAssociation("entries");
    }

}

// end - wizard
?>