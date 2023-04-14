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

class LayerGroupMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'layergroup',
            'attributes' => array(
                'idLayerGroup' => array('column' => 'idLayerGroup','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
            ),
            'associations' => array(
                'layertypes' => array('toClass' => 'fnbr\models\LayerType', 'cardinality' => 'oneToMany' , 'keys' => 'idLayerGroup:idLayerGroup'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idLayerGroup;
    /**
     * 
     * @var string 
     */
    protected $name;

    /**
     * Associations
     */
    protected $layertypes;
    

    /**
     * Getters/Setters
     */
    public function getIdLayerGroup() {
        return $this->idLayerGroup;
    }

    public function setIdLayerGroup($value) {
        $this->idLayerGroup = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
    }
    /**
     *
     * @return Association
     */
    public function getLayertypes() {
        if (is_null($this->layertypes)){
            $this->retrieveAssociation("layertypes");
        }
        return  $this->layertypes;
    }
    /**
     *
     * @param Association $value
     */
    public function setLayertypes($value) {
        $this->layertypes = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationLayertypes() {
        $this->retrieveAssociation("layertypes");
    }

    

}
// end - wizard

?>