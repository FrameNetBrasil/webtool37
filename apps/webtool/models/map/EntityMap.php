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

class EntityMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'entity',
            'attributes' => array(
                'idEntity' => array('column' => 'idEntity','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'alias' => array('column' => 'alias','type' => 'string'),
                'type' => array('column' => 'type','type' => 'string'),
                'idOld' => array('column' => 'idOld','type' => 'integer'),
            ),
            'associations' => array(
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idEntity;
    /**
     * 
     * @var string 
     */
    protected $alias;
    /**
     * 
     * @var string 
     */
    protected $type;
    /**
     * 
     * @var integer 
     */
    protected $idOld;

    /**
     * Getters/Setters
     */
    public function getIdEntity() {
        return $this->idEntity;
    }

    public function setIdEntity($value) {
        $this->idEntity = $value;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function setAlias($value) {
        $this->alias = $value;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($value) {
        $this->type = $value;
    }

    public function getIdOld() {
        return $this->idOld;
    }

    public function setIdOld($value) {
        $this->idOld = $value;
    }

}
// end - wizard

?>