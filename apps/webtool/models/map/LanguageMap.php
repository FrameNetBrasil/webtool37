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

class LanguageMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'language',
            'attributes' => array(
                'idLanguage' => array('column' => 'idLanguage','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'language' => array('column' => 'language','type' => 'string'),
                'description' => array('column' => 'description','type' => 'string'),
            ),
            'associations' => array(
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idLanguage;
    /**
     * 
     * @var string 
     */
    protected $language;
    /**
     * 
     * @var string 
     */
    protected $description;

    /**
     * Associations
     */
    

    /**
     * Getters/Setters
     */
    public function getIdLanguage() {
        return $this->idLanguage;
    }

    public function setIdLanguage($value) {
        $this->idLanguage = $value;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($value) {
        $this->language = $value;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($value) {
        $this->description = $value;
    }

    

}
// end - wizard

?>