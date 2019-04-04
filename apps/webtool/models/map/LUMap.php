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

class LUMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'lu',
            'attributes' => array(
                'idLU' => array('column' => 'idLU','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
                'senseDescription' => array('column' => 'senseDescription','type' => 'string'),
                'active' => array('column' => 'active','type' => 'integer'),
                'importNum' => array('column' => 'importNum','type' => 'integer'),
                'incorporatedFE' => array('column' => 'incorporatedFE','type' => 'integer'),
                'bff' => array('column' => 'bff','type' => 'integer'),
                'bffOther' => array('column' => 'bffOther','type' => 'string'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
                'idLemma' => array('column' => 'idLemma','type' => 'integer'),
            ),
            'associations' => array(
                'entity' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity:idEntity'), 
                'lemma' => array('toClass' => 'fnbr\models\Lemma', 'cardinality' => 'oneToOne' , 'keys' => 'idLemma:idLemma'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idLU;
    /**
     * 
     * @var string 
     */
    protected $name;
    /**
     * 
     * @var string 
     */
    protected $senseDescription;
    /**
     * 
     * @var integer 
     */
    protected $active;
    /**
     * 
     * @var integer 
     */
    protected $importNum;
    /**
     * 
     * @var integer 
     */
    protected $incorporatedFE;
    /**
     *
     * @var integer
     */
    protected $bff;
    /**
     *
     * @var string
     */
    protected $bffOther;
    /**
     * 
     * @var integer 
     */
    protected $idEntity;
    /**
     * 
     * @var integer 
     */
    protected $idLemma;

    /**
     * Associations
     */
    protected $entity;
    protected $lemma;
    

    /**
     * Getters/Setters
     */
    public function getIdLU() {
        return $this->idLU;
    }

    public function setIdLU($value) {
        $this->idLU = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
    }

    public function getSenseDescription() {
        return $this->senseDescription;
    }

    public function setSenseDescription($value) {
        $this->senseDescription = $value;
    }

    public function getActive() {
        return $this->active;
    }

    public function setActive($value) {
        $this->active = $value;
    }

    public function getImportNum() {
        return $this->importNum;
    }

    public function setImportNum($value) {
        $this->importNum = $value;
    }

    public function getIncorporatedFE() {
        return $this->incorporatedFE;
    }

    public function setIncorporatedFE($value) {
        $this->incorporatedFE = $value;
    }

    public function getBFF() {
        return $this->bff;
    }

    public function setBFF($value) {
        $this->bff = $value;
    }

    public function getBffOther() {
        return $this->bffOther;
    }

    public function setBffOther($value) {
        $this->bffOther = $value;
    }

    public function getIdEntity() {
        return $this->idEntity;
    }

    public function setIdEntity($value) {
        $this->idEntity = $value;
    }

    public function getIdLemma() {
        return $this->idLemma;
    }

    public function setIdLemma($value) {
        $this->idLemma = $value;
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
    public function getLemma() {
        if (is_null($this->lemma)){
            $this->retrieveAssociation("lemma");
        }
        return  $this->lemma;
    }
    /**
     *
     * @param Association $value
     */
    public function setLemma($value) {
        $this->lemma = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationLemma() {
        $this->retrieveAssociation("lemma");
    }

    

}
// end - wizard

?>