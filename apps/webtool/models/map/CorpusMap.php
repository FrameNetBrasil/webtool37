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

class CorpusMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'corpus',
            'attributes' => array(
                'idCorpus' => array('column' => 'idCorpus','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'active' => array('column' => 'active','type' => 'integer'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
            ),
            'associations' => array(
                'entity' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity:idEntity'),
                'documents' => array('toClass' => 'fnbr\models\Document', 'cardinality' => 'oneToMany' , 'keys' => 'idCorpus:idCorpus'),
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany' , 'keys' => 'entry:entry'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idCorpus;
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
     * Associations
     */
    protected $documents;
    protected $entries;
    

    /**
     * Getters/Setters
     */
    public function getIdCorpus() {
        return $this->idCorpus;
    }

    public function setIdCorpus($value) {
        $this->idCorpus = $value;
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

    /**
     *
     * @return Association
     */
    public function getDocuments() {
        if (is_null($this->documents)){
            $this->retrieveAssociation("documents");
        }
        return  $this->documents;
    }
    /**
     *
     * @param Association $value
     */
    public function setDocuments($value) {
        $this->documents = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationDocuments() {
        $this->retrieveAssociation("documents");
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

    protected $idEntity;
    protected $entity;

    public function getIdEntity() {
        return $this->idEntity;
    }

    public function setIdEntity($value) {
        $this->idEntity = $value;
    }

    public function setEntity($value) {
        $this->entity = $value;
    }

    public function getAssociationEntity() {
        $this->retrieveAssociation("entity");
    }



}
// end - wizard
