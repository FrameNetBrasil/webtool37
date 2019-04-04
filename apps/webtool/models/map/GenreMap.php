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

class GenreMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'genre',
            'attributes' => array(
                'idGenre' => array('column' => 'idGenre','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'idGenreType' => array('column' => 'idGenreType','type' => 'integer','key' => 'foreign'),
            ),
            'associations' => array(
                'genreType' => array('toClass' => 'fnbr\models\GenreType', 'cardinality' => 'oneToOne' , 'keys' => 'idGenreType:idGenreType'),
                'documents' => array('toClass' => 'fnbr\models\Document', 'cardinality' => 'oneToMany' , 'keys' => 'idGenre:idGenre'),
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany' , 'keys' => 'entry:entry'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idGenre;
    /**
     * 
     * @var string 
     */
    protected $entry;
    /**
     *
     * @var integer
     */
    protected $idGenreType;

    /**
     * Associations
     */
    protected $documents;
    protected $entries;
    protected $genreType;


    /**
     * Getters/Setters
     */
    public function getIdGenre() {
        return $this->idGenre;
    }

    public function setIdGenre($value) {
        $this->idGenre = $value;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setEntry($value) {
        $this->entry = $value;
    }
    public function getIdGenreType() {
        return $this->idGenreType;
    }

    public function setIdGenreType($value) {
        $this->idGenreType = $value;
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
    /**
     *
     * @return Association
     */
    public function getGenreType() {
        if (is_null($this->genreType)){
            $this->retrieveAssociation("genreType");
        }
        return  $this->genreType;
    }
    /**
     *
     * @param Association $value
     */
    public function setGenreType($value) {
        $this->genreType = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationGenreType() {
        $this->retrieveAssociation("genreType");
    }

    

}
// end - wizard
