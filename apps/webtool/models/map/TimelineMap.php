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

class TimelineMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'timeline',
            'attributes' => array(
                'idTimeline' => array('column' => 'idTimeline','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'timeline' => array('column' => 'timeline','type' => 'string'),
                'numOrder' => array('column' => 'numorder','type' => 'integer'),
                'tlDateTime' => array('column' => 'tldateTime','type' => 'timestamp'),
                'author' => array('column' => 'author','type' => 'string'),
                'operation' => array('column' => 'operation','type' => 'string'),
            ),
            'associations' => array(
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idTimeline;
    /**
     * 
     * @var string 
     */
    protected $timeline;
    /**
     * 
     * @var integer 
     */
    protected $numOrder;
    /**
     * 
     * @var date 
     */
    protected $tlDateTime;
    /**
     * 
     * @var string 
     */
    protected $author;
    /**
     *
     * @var string
     */
    protected $operation;

    /**
     * Associations
     */
    

    /**
     * Getters/Setters
     */
    public function getIdTimeline() {
        return $this->idTimeline;
    }

    public function setIdTimeline($value) {
        $this->idTimeline = $value;
    }

    public function getTimeline() {
        return $this->timeline;
    }

    public function setTimeline($value) {
        $this->timeline = $value;
    }

    public function getNumOrder() {
        return $this->numOrder;
    }

    public function setNumOrder($value) {
        $this->numOrder = $value;
    }

    public function getTlDateTime() {
        return $this->tlDateTime;
    }

    public function setTlDateTime($value) {
        if (!($value instanceof \MTimestamp)) {
            $value = new \MTimestamp($value);
        }
        $this->tlDateTime = $value;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($value) {
        $this->author = $value;
    }

    public function getOperation() {
        return $this->operation;
    }

    public function setOperation($value) {
        $this->operation = $value;
    }

    

}
// end - wizard

?>