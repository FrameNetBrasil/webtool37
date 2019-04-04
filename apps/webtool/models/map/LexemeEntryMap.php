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

class LexemeEntryMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'lexemeentry',
            'attributes' => array(
                'idLexemeEntry' => array('column' => 'idLexemeEntry','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'lexemeOrder' => array('column' => 'lexemeOrder','type' => 'integer'),
                'breakBefore' => array('column' => 'breakBefore','type' => 'integer'),
                'headWord' => array('column' => 'headWord','type' => 'integer'),
                'idLexeme' => array('column' => 'idLexeme','type' => 'integer'),
                'idLemma' => array('column' => 'idLemma','type' => 'integer'),
            ),
            'associations' => array(
                'lexeme' => array('toClass' => 'fnbr\models\Lexeme', 'cardinality' => 'oneToOne' , 'keys' => 'idLexeme:idLexeme'), 
                'lemma' => array('toClass' => 'fnbr\models\Lemma', 'cardinality' => 'oneToOne' , 'keys' => 'idLemma:idLemma'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idLexemeEntry;
    /**
     * 
     * @var integer 
     */
    protected $lexemeOrder;
    /**
     * 
     * @var integer 
     */
    protected $breakBefore;
    /**
     * 
     * @var integer 
     */
    protected $headWord;
    /**
     * 
     * @var integer 
     */
    protected $idLexeme;
    /**
     * 
     * @var integer 
     */
    protected $idLemma;

    /**
     * Associations
     */
    protected $lexeme;
    protected $lemma;
    

    /**
     * Getters/Setters
     */
    public function getIdLexemeEntry() {
        return $this->idLexemeEntry;
    }

    public function setIdLexemeEntry($value) {
        $this->idLexemeEntry = $value;
    }

    public function getLexemeOrder() {
        return $this->lexemeOrder;
    }

    public function setLexemeOrder($value) {
        $this->lexemeOrder = $value;
    }

    public function getBreakBefore() {
        return $this->breakBefore;
    }

    public function setBreakBefore($value) {
        $this->breakBefore = $value;
    }

    public function getHeadWord() {
        return $this->headWord;
    }

    public function setHeadWord($value) {
        $this->headWord = $value;
    }

    public function getIdLexeme() {
        return $this->idLexeme;
    }

    public function setIdLexeme($value) {
        $this->idLexeme = $value;
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
    public function getLexeme() {
        if (is_null($this->lexeme)){
            $this->retrieveAssociation("lexeme");
        }
        return  $this->lexeme;
    }
    /**
     *
     * @param Association $value
     */
    public function setLexeme($value) {
        $this->lexeme = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationLexeme() {
        $this->retrieveAssociation("lexeme");
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