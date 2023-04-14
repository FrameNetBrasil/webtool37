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

class ColorMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'color',
            'attributes' => array(
                'idColor' => array('column' => 'idColor','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
                'rgbFg' => array('column' => 'rgbFg','type' => 'string'),
                'rgbBg' => array('column' => 'rgbBg','type' => 'string'),
            ),
            'associations' => array(
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idColor;
    /**
     * 
     * @var string 
     */
    protected $name;
    /**
     * 
     * @var string 
     */
    protected $rgbFg;
    /**
     * 
     * @var string 
     */
    protected $rgbBg;

    /**
     * Associations
     */
    

    /**
     * Getters/Setters
     */
    public function getIdColor() {
        return $this->idColor;
    }

    public function setIdColor($value) {
        $this->idColor = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
    }

    public function getRgbFg() {
        return $this->rgbFg;
    }

    public function setRgbFg($value) {
        $this->rgbFg = $value;
    }

    public function getRgbBg() {
        return $this->rgbBg;
    }

    public function setRgbBg($value) {
        $this->rgbBg = $value;
    }

    

}
// end - wizard

?>