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

class PropertyMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'property',
            'attributes' => array(
                'idProperty' => array('column' => 'idProperty','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'idEntity' => array('column' => 'idEntity','key' => 'foreign','type' => 'string'),
            ),
            'associations' => array(
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idProperty;
    /**
     * 
     * @var string 
     */
    protected $entry;
    /**
     * 
     * @var string 
     */
    protected $idEntity;

    /**
     * Associations
     */
    

    /**
     * Getters/Setters
     */
    public function getIdProperty() {
        return $this->idProperty;
    }

    public function setIdProperty($value) {
        $this->idProperty = $value;
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

    

}
// end - wizard

?>