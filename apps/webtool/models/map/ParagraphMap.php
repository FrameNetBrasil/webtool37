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

class ParagraphMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'paragraph',
            'attributes' => array(
                'idParagraph' => array('column' => 'idParagraph','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'documentOrder' => array('column' => 'documentOrder','type' => 'integer'),
                'idDocument' => array('column' => 'idDocument','type' => 'integer'),
            ),
            'associations' => array(
                'document' => array('toClass' => 'fnbr\models\Document', 'cardinality' => 'oneToOne' , 'keys' => 'idDocument:idDocument'), 
                'sentences' => array('toClass' => 'fnbr\models\Sentence', 'cardinality' => 'oneToMany' , 'keys' => 'idParagraph:idParagraph'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idParagraph;
    /**
     * 
     * @var integer 
     */
    protected $documentOrder;
    /**
     * 
     * @var integer 
     */
    protected $idDocument;

    /**
     * Associations
     */
    protected $document;
    protected $sentences;
    

    /**
     * Getters/Setters
     */
    public function getIdParagraph() {
        return $this->idParagraph;
    }

    public function setIdParagraph($value) {
        $this->idParagraph = $value;
    }

    public function getDocumentOrder() {
        return $this->documentOrder;
    }

    public function setDocumentOrder($value) {
        $this->documentOrder = $value;
    }

    public function getIdDocument() {
        return $this->idDocument;
    }

    public function setIdDocument($value) {
        $this->idDocument = $value;
    }
    /**
     *
     * @return Association
     */
    public function getDocument() {
        if (is_null($this->document)){
            $this->retrieveAssociation("document");
        }
        return  $this->document;
    }
    /**
     *
     * @param Association $value
     */
    public function setDocument($value) {
        $this->document = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationDocument() {
        $this->retrieveAssociation("document");
    }
    /**
     *
     * @return Association
     */
    public function getSentences() {
        if (is_null($this->sentences)){
            $this->retrieveAssociation("sentences");
        }
        return  $this->sentences;
    }
    /**
     *
     * @param Association $value
     */
    public function setSentences($value) {
        $this->sentences = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationSentences() {
        $this->retrieveAssociation("sentences");
    }

    

}
// end - wizard

?>