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

class TranslationMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'translation',
            'attributes' => array(
                'idTranslation' => array('column' => 'idTranslation','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'resource' => array('column' => 'resource','type' => 'string'),
                'text' => array('column' => 'text','type' => 'string'),
                'idLanguage' => array('column' => 'idLanguage','type' => 'integer'),
            ),
            'associations' => array(
                'language' => array('toClass' => 'fnbr\models\Language', 'cardinality' => 'oneToOne' , 'keys' => 'idLanguage:idLanguage'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idTranslation;
    /**
     * 
     * @var string 
     */
    protected $resource;
    /**
     * 
     * @var string 
     */
    protected $text;
    /**
     * 
     * @var integer 
     */
    protected $idLanguage;

    /**
     * Associations
     */
    protected $language;
    

    /**
     * Getters/Setters
     */
    public function getIdTranslation() {
        return $this->idTranslation;
    }

    public function setIdTranslation($value) {
        $this->idTranslation = $value;
    }

    public function getResource() {
        return $this->resource;
    }

    public function setResource($value) {
        $this->resource = $value;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($value) {
        $this->text = $value;
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

    

}
// end - wizard

?>