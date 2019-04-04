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

class EntryMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'entry',
            'attributes' => array(
                'idEntry' => array('column' => 'idEntry','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'name' => array('column' => 'name','type' => 'string'),
                'description' => array('column' => 'description','type' => 'string'),
                'nick' => array('column' => 'nick','type' => 'string'),
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
    protected $idEntry;
    /**
     * 
     * @var string 
     */
    protected $entry;
    /**
     * 
     * @var string 
     */
    protected $name;
    /**
     * 
     * @var string 
     */
    protected $description;
    /**
     * 
     * @var string 
     */
    protected $nick;
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
    public function getIdEntry() {
        return $this->idEntry;
    }

    public function setIdEntry($value) {
        $this->idEntry = $value;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setEntry($value) {
        $this->entry = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($value) {
        $this->description = $value;
    }

    public function getNick() {
        return $this->nick;
    }

    public function setNick($value) {
        $this->nick = $value;
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