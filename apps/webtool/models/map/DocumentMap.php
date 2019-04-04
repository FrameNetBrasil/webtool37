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

class DocumentMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'document',
            'attributes' => array(
                'idDocument' => array('column' => 'idDocument','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'author' => array('column' => 'author','type' => 'string'),
                'timeline' => array('column' => 'timeline','type' => 'string'),
                'idGenre' => array('column' => 'idGenre','type' => 'integer'),
                'idCorpus' => array('column' => 'idCorpus','type' => 'integer'),
            ),
            'associations' => array(
                'genre' => array('toClass' => 'fnbr\models\Genre', 'cardinality' => 'oneToOne' , 'keys' => 'idGenre:idGenre'), 
                'corpus' => array('toClass' => 'fnbr\models\Corpus', 'cardinality' => 'oneToOne' , 'keys' => 'idCorpus:idCorpus'), 
                'paragraphs' => array('toClass' => 'fnbr\models\Paragraph', 'cardinality' => 'oneToMany' , 'keys' => 'idDocument:idDocument'), 
                'timelines' => array('toClass' => 'fnbr\models\Timeline', 'cardinality' => 'oneToMany' , 'keys' => 'timeline:timeline'), 
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany' , 'keys' => 'entry:entry'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idDocument;
    /**
     * 
     * @var string 
     */
    protected $entry;
    /**
     * 
     * @var string 
     */
    protected $author;
    /**
     * 
     * @var string 
     */
    protected $timeline;
    /**
     * 
     * @var integer 
     */
    protected $idGenre;
    /**
     * 
     * @var integer 
     */
    protected $idCorpus;

    /**
     * Associations
     */
    protected $genre;
    protected $corpus;
    protected $paragraphs;
    protected $timelines;
    protected $entries;
    

    /**
     * Getters/Setters
     */
    public function getIdDocument() {
        return $this->idDocument;
    }

    public function setIdDocument($value) {
        $this->idDocument = $value;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setEntry($value) {
        $this->entry = $value;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($value) {
        $this->author = $value;
    }

    public function getTimeline() {
        return $this->timeline;
    }

    public function setTimeline($value) {
        $this->timeline = $value;
    }

    public function getIdGenre() {
        return $this->idGenre;
    }

    public function setIdGenre($value) {
        $this->idGenre = $value;
    }

    public function getIdCorpus() {
        return $this->idCorpus;
    }

    public function setIdCorpus($value) {
        $this->idCorpus = $value;
    }
    /**
     *
     * @return Association
     */
    public function getGenre() {
        if (is_null($this->genre)){
            $this->retrieveAssociation("genre");
        }
        return  $this->genre;
    }
    /**
     *
     * @param Association $value
     */
    public function setGenre($value) {
        $this->genre = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationGenre() {
        $this->retrieveAssociation("genre");
    }
    /**
     *
     * @return Association
     */
    public function getCorpus() {
        if (is_null($this->corpus)){
            $this->retrieveAssociation("corpus");
        }
        return  $this->corpus;
    }
    /**
     *
     * @param Association $value
     */
    public function setCorpus($value) {
        $this->corpus = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationCorpus() {
        $this->retrieveAssociation("corpus");
    }
    /**
     *
     * @return Association
     */
    public function getParagraphs() {
        if (is_null($this->paragraphs)){
            $this->retrieveAssociation("paragraphs");
        }
        return  $this->paragraphs;
    }
    /**
     *
     * @param Association $value
     */
    public function setParagraphs($value) {
        $this->paragraphs = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationParagraphs() {
        $this->retrieveAssociation("paragraphs");
    }
    /**
     *
     * @return Association
     */
    public function getTimelines() {
        if (is_null($this->timelines)){
            $this->retrieveAssociation("timelines");
        }
        return  $this->timelines;
    }
    /**
     *
     * @param Association $value
     */
    public function setTimelines($value) {
        $this->timelines = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationTimelines() {
        $this->retrieveAssociation("timelines");
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