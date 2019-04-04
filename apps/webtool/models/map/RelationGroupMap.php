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

class RelationGroupMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'relationgroup',
            'attributes' => array(
                'idRelationGroup' => array('column' => 'idRelationGroup','key' => 'primary','idgenerator' => 'identity','type' => 'string'),
                'entry' => array('column' => 'entry','type' => 'string'),
            ),
            'associations' => array(
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany' , 'keys' => 'entry:entry'), 
            )
        );
    }
    
    /**
     * 
     * @var string 
     */
    protected $idRelationGroup;
    /**
     * 
     * @var string 
     */
    protected $entry;

    /**
     * Associations
     */
    

    /**
     * Getters/Setters
     */
    public function getIdRelationGroup() {
        return $this->idRelationGroup;
    }

    public function setIdRelationGroup($value) {
        $this->idRelationGroup = $value;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setEntry($value) {
        $this->entry = $value;
    }

    /**
     *
     * @return Association
     */
    public function getEntries() {
        if (is_null($this->entries)){
            $this->retrieveAssociation("entries");
        }
        return  $this->entries;
    }
    /**
     *
     * @param Association $value
     */
    public function setEntries($value) {
        $this->entries = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationEntries() {
        $this->retrieveAssociation("entries");
    }

    

}
// end - wizard

