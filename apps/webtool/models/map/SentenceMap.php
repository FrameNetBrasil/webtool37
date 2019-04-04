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

class SentenceMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'sentence',
            'attributes' => array(
                'idSentence' => array('column' => 'idSentence','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'text' => array('column' => 'text','type' => 'string'),
                'paragraphOrder' => array('column' => 'paragraphOrder','type' => 'integer'),
                'timeline' => array('column' => 'timeline','type' => 'string'),
                'idParagraph' => array('column' => 'idParagraph','type' => 'integer'),
                'idLanguage' => array('column' => 'idLanguage','type' => 'integer'),
            ),
            'associations' => array(
                'paragraph' => array('toClass' => 'fnbr\models\Paragraph', 'cardinality' => 'oneToOne' , 'keys' => 'idParagraph:idParagraph'), 
                'language' => array('toClass' => 'fnbr\models\Language', 'cardinality' => 'oneToOne' , 'keys' => 'idLanguage:idLanguage'), 
                'annotationsets' => array('toClass' => 'fnbr\models\AnnotationSet', 'cardinality' => 'oneToMany' , 'keys' => 'idSentence:idSentence'), 
                'timelines' => array('toClass' => 'fnbr\models\Timeline', 'cardinality' => 'oneToMany' , 'keys' => 'timeline:timeline'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idSentence;
    /**
     * 
     * @var string 
     */
    protected $text;
    /**
     * 
     * @var integer 
     */
    protected $paragraphOrder;
    /**
     * 
     * @var string 
     */
    protected $timeline;
    /**
     * 
     * @var integer 
     */
    protected $idParagraph;
    /**
     * 
     * @var integer 
     */
    protected $idLanguage;

    /**
     * Associations
     */
    protected $paragraph;
    protected $language;
    protected $annotationsets;
    protected $timelines;
    

    /**
     * Getters/Setters
     */
    public function getIdSentence() {
        return $this->idSentence;
    }

    public function setIdSentence($value) {
        $this->idSentence = $value;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($value) {
        $this->text = $value;
    }

    public function getParagraphOrder() {
        return $this->paragraphOrder;
    }

    public function setParagraphOrder($value) {
        $this->paragraphOrder = $value;
    }

    public function getTimeline() {
        return $this->timeline;
    }

    public function setTimeline($value) {
        $this->timeline = $value;
    }

    public function getIdParagraph() {
        return $this->idParagraph;
    }

    public function setIdParagraph($value) {
        $this->idParagraph = $value;
    }

    public function getIdLanguage() {
        return $this->idLanguage;
    }

    public function setIdLanguage($value) {
        $this->idLanguage = $value;
    }
    /**
     *
     * @return Association
     */
    public function getParagraph() {
        if (is_null($this->paragraph)){
            $this->retrieveAssociation("paragraph");
        }
        return  $this->paragraph;
    }
    /**
     *
     * @param Association $value
     */
    public function setParagraph($value) {
        $this->paragraph = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationParagraph() {
        $this->retrieveAssociation("paragraph");
    }
    /**
     *
     * @return Association
     */
    public function getLanguage() {
        if (is_null($this->language)){
            $this->retrieveAssociation("language");
        }
        return  $this->language;
    }
    /**
     *
     * @param Association $value
     */
    public function setLanguage($value) {
        $this->language = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationLanguage() {
        $this->retrieveAssociation("language");
    }
    /**
     *
     * @return Association
     */
    public function getAnnotationsets() {
        if (is_null($this->annotationsets)){
            $this->retrieveAssociation("annotationsets");
        }
        return  $this->annotationsets;
    }
    /**
     *
     * @param Association $value
     */
    public function setAnnotationsets($value) {
        $this->annotationsets = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationAnnotationsets() {
        $this->retrieveAssociation("annotationsets");
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

    

}
// end - wizard

?>