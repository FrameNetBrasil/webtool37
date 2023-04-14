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

class POSMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'pos',
            'attributes' => array(
                'idPOS' => array('column' => 'idPOS','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'POS' => array('column' => 'POS','type' => 'string'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
            ),
            'associations' => array(
                'entity' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity:idEntity'), 
                'lemmas' => array('toClass' => 'fnbr\models\Lemma', 'cardinality' => 'oneToMany' , 'keys' => 'idPOS:idPOS'), 
                'lexemes' => array('toClass' => 'fnbr\models\Lexeme', 'cardinality' => 'oneToMany' , 'keys' => 'idPOS:idPOS'), 
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany' , 'keys' => 'entry:entry'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idPOS;
    /**
     * 
     * @var string 
     */
    protected $POS;
    /**
     * 
     * @var string 
     */
    protected $entry;
    /**
     * 
     * @var string 
     */
    protected $timeline;

    /**
     * Associations
     */
    protected $lemmas;
    protected $lexemes;
    protected $timelines;
    protected $entries;
    

    /**
     * Getters/Setters
     */
    public function getIdPOS() {
        return $this->idPOS;
    }

    public function setIdPOS($value) {
        $this->idPOS = $value;
    }

    public function getPOS() {
        return $this->POS;
    }

    public function setPOS($value) {
        $this->POS = $value;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setEntry($value) {
        $this->entry = $value;
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
    public function getLemmas() {
        if (is_null($this->lemmas)){
            $this->retrieveAssociation("lemmas");
        }
        return  $this->lemmas;
    }
    /**
     *
     * @param Association $value
     */
    public function setLemmas($value) {
        $this->lemmas = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationLemmas() {
        $this->retrieveAssociation("lemmas");
    }
    /**
     *
     * @return Association
     */
    public function getLexemes() {
        if (is_null($this->lexemes)){
            $this->retrieveAssociation("lexemes");
        }
        return  $this->lexemes;
    }
    /**
     *
     * @param Association $value
     */
    public function setLexemes($value) {
        $this->lexemes = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationLexemes() {
        $this->retrieveAssociation("lexemes");
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