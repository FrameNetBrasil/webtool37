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

class LayerTypeMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'layertype',
            'attributes' => array(
                'idLayerType' => array('column' => 'idLayerType','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'entry' => array('column' => 'entry','type' => 'string'),
                'allowsApositional' => array('column' => 'allowsApositional','type' => 'integer'),
                'isAnnotation' => array('column' => 'isAnnotation','type' => 'integer'),
                'layerOrder' => array('column' => 'layerOrder','type' => 'integer'),
                'idLayerGroup' => array('column' => 'idLayerGroup','type' => 'integer'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
            ),
            'associations' => array(
                'layergroup' => array('toClass' => 'fnbr\models\LayerGroup', 'cardinality' => 'oneToOne' , 'keys' => 'idLayerGroup:idLayerGroup'), 
                'entity' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity:idEntity'), 
                'layers' => array('toClass' => 'fnbr\models\Layer', 'cardinality' => 'oneToMany' , 'keys' => 'idLayerType:idLayerType'), 
                'entries' => array('toClass' => 'fnbr\models\Entry', 'cardinality' => 'oneToMany' , 'keys' => 'entry:entry'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idLayerType;
    /**
     * 
     * @var string 
     */
    protected $entry;
    /**
     * 
     * @var integer 
     */
    protected $allowsApositional;
    /**
     * 
     * @var integer 
     */
    protected $isAnnotation;
    /**
     * 
     * @var integer 
     */
    protected $layerOrder;
    /**
     * 
     * @var integer 
     */
    protected $idLayerGroup;
    /**
     * 
     * @var integer 
     */
    protected $idEntity;
    /**
     * 
     * @var integer 
     */
    protected $idLanguage;

    /**
     * Associations
     */
    protected $layergroup;
    protected $entity;
    protected $language;
    protected $layers;
    protected $entries;
    

    /**
     * Getters/Setters
     */
    public function getIdLayerType() {
        return $this->idLayerType;
    }

    public function setIdLayerType($value) {
        $this->idLayerType = $value;
    }

    public function getEntry() {
        return $this->entry;
    }

    public function setEntry($value) {
        $this->entry = $value;
    }

    public function getAllowsApositional() {
        return $this->allowsApositional;
    }

    public function setAllowsApositional($value) {
        $this->allowsApositional = $value;
    }

    public function getIsAnnotation() {
        return $this->isAnnotation;
    }

    public function setIsAnnotation($value) {
        $this->isAnnotation = $value;
    }

    public function getLayerOrder() {
        return $this->order;
    }

    public function setLayerOrder($value) {
        $this->order = $value;
    }

    public function getIdLayerGroup() {
        return $this->idLayerGroup;
    }

    public function setIdLayerGroup($value) {
        $this->idLayerGroup = $value;
    }

    public function getIdEntity() {
        return $this->idEntity;
    }

    public function setIdEntity($value) {
        $this->idEntity = $value;
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
    public function getLayergroup() {
        if (is_null($this->layergroup)){
            $this->retrieveAssociation("layergroup");
        }
        return  $this->layergroup;
    }
    /**
     *
     * @param Association $value
     */
    public function setLayergroup($value) {
        $this->layergroup = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationLayergroup() {
        $this->retrieveAssociation("layergroup");
    }
    /**
     *
     * @return Association
     */
    public function getEntity() {
        if (is_null($this->entity)){
            $this->retrieveAssociation("entity");
        }
        return  $this->entity;
    }
    /**
     *
     * @param Association $value
     */
    public function setEntity($value) {
        $this->entity = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationEntity() {
        $this->retrieveAssociation("entity");
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
    /**
     *
     * @return Association
     */
    public function getLayers() {
        if (is_null($this->layers)){
            $this->retrieveAssociation("layers");
        }
        return  $this->layers;
    }
    /**
     *
     * @param Association $value
     */
    public function setLayers($value) {
        $this->layers = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationLayers() {
        $this->retrieveAssociation("layers");
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

?>