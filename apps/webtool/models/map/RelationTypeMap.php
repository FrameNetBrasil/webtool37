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

class RelationTypeMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'relationtype',
            'attributes' => array(
                'idRelationType' => array('column' => 'idRelationType','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'nameEntity1' => array('column' => 'nameEntity1','type' => 'string'),
                'nameEntity2' => array('column' => 'nameEntity2','type' => 'string'),
                'idRelationGroup' => array('column' => 'idRelationGroup','type' => 'integer'),
                'idDomain' => array('column' => 'idDomain','type' => 'integer'),
            ),
            'associations' => array(
                'domain' => array('toClass' => 'fnbr\models\Domain', 'cardinality' => 'oneToOne' , 'keys' => 'idDomain:idDomain'), 
                'relationgroup' => array('toClass' => 'fnbr\models\RelationGroup', 'cardinality' => 'oneToOne' , 'keys' => 'idRelationGroup:idRelationGroup'), 
                'entityrelations' => array('toClass' => 'fnbr\models\EntityRelation', 'cardinality' => 'oneToMany' , 'keys' => 'idRelationType:idRelationType'), 
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany' , 'keys' => 'entry:entry'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idRelationType;
    /**
     * 
     * @var string 
     */
    protected $entry;
    /**
     * 
     * @var string 
     */
    protected $nameEntity1;
    /**
     * 
     * @var string 
     */
    protected $nameEntity2;
    /**
     * 
     * @var integer 
     */
    protected $idRelationGroup;
    /**
     * 
     * @var integer 
     */
    protected $idDomain;

    /**
     * Associations
     */
    protected $domain;
    protected $relationgroup;
    protected $entityrelations;
    protected $entries;
    

    /**
     * Getters/Setters
     */
    public function getIdRelationType() {
        return $this->idRelationType;
    }

    public function setIdRelationType($value) {
        $this->idRelationType = $value;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setEntry($value) {
        $this->entry = $value;
    }

    public function getNameEntity1() {
        return $this->nameEntity1;
    }

    public function setNameEntity1($value) {
        $this->nameEntity1 = $value;
    }

    public function getNameEntity2() {
        return $this->nameEntity2;
    }

    public function setNameEntity2($value) {
        $this->nameEntity2 = $value;
    }

    public function getIdRelationGroup() {
        return $this->idRelationGroup;
    }

    public function setIdRelationGroup($value) {
        $this->idRelationGroup = $value;
    }

    public function getIdDomain() {
        return $this->idDomain;
    }

    public function setIdDomain($value) {
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
    public function getRelationGroup() {
        if (is_null($this->relationgroup)){
            $this->retrieveAssociation("relationgroup");
        }
        return  $this->domain;
    }
    /**
     *
     * @param Association $value
     */
    public function setRelationGroup($value) {
        $this->relationgroup = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationRelationGroup() {
        $this->retrieveAssociation("relationgroup");
    }
    /**
     *
     * @return Association
     */
    public function getEntityrelations() {
        if (is_null($this->entityrelations)){
            $this->retrieveAssociation("entityrelations");
        }
        return  $this->entityrelations;
    }
    /**
     *
     * @param Association $value
     */
    public function setEntityrelations($value) {
        $this->entityrelations = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationEntityrelations() {
        $this->retrieveAssociation("entityrelations");
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