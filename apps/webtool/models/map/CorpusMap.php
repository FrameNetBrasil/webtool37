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
                'timeline' => array('column' => 'timeline','type' => 'string'),
            ),
            'associations' => array(
                'documents' => array('toClass' => 'fnbr\models\Document', 'cardinality' => 'oneToMany' , 'keys' => 'idCorpus:idCorpus'), 
                'timelines' => array('toClass' => 'fnbr\models\Timeline', 'cardinality' => 'oneToMany' , 'keys' => 'timeline:timeline'), 
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
     * @var string 
     */
    protected $timeline;

    /**
     * Associations
     */
    protected $documents;
    protected $timelines;
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

    public function getTimeline() {
        return $this->timeline;
    }

    public function setTimeline($value) {
        $this->timeline = $value;
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