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

class DocumentMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'documentmm',
            'attributes' => array(
                'idDocumentMM' => array('column' => 'idDocument','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'audioPath' => array('column' => 'audioPath','type' => 'string'),
                'visualPath' => array('column' => 'visualPath','type' => 'string'),
                'alignPath' => array('column' => 'alignPath','type' => 'string'),
                'idDocument' => array('column' => 'idDocument','type' => 'integer'),
            ),
            'associations' => array(
                'document' => array('toClass' => 'fnbr\models\Document', 'cardinality' => 'oneToOne' , 'keys' => 'idDocument:idDocument'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idDocumentMM;
    /**
     * 
     * @var string 
     */
    protected $audioPath;
    /**
     * 
     * @var string 
     */
    protected $visualPath;
    /**
     * 
     * @var string 
     */
    protected $alignPath;
    /**
     * 
     * @var integer 
     */
    protected $idDocument;

    /**
     * Associations
     */
    protected $document;


    /**
     * Getters/Setters
     */
    public function getIdDocumentMM() {
        return $this->idDocumentMM;
    }

    public function setIdDocumentMM($value) {
        $this->idDocumentMM = $value;
    }

    public function getAudioPath() {
        return $this->audioPath;
    }

    public function setAudioPath($value) {
        $this->audioPath = $value;
    }

    public function getVisualPath() {
        return $this->visualPath;
    }

    public function setVisualPath($value) {
        $this->visualPath = $value;
    }

    public function getAlignPath() {
        return $this->alignPath;
    }

    public function setAlignPath($value) {
        $this->alignPath = $value;
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


}
// end - wizard