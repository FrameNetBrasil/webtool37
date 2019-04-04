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

class LabelMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'label',
            'attributes' => array(
                'idLabel' => array('column' => 'idLabel','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'startChar' => array('column' => 'startChar','type' => 'integer'),
                'endChar' => array('column' => 'endChar','type' => 'integer'),
                'multi' => array('column' => 'multi','type' => 'integer'),
                'idLabelType' => array('column' => 'idLabelType','type' => 'integer'),
                'idLayer' => array('column' => 'idLayer','type' => 'integer'),
                'idInstantiationType' => array('column' => 'idInstantiationType','type' => 'integer'),
            ),
            'associations' => array(
                'entity' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idLabelType:idEntity'), 
                'layer' => array('toClass' => 'fnbr\models\Layer', 'cardinality' => 'oneToOne' , 'keys' => 'idLayer:idLayer'), 
                'instantiationType' => array('toClass' => 'fnbr\models\TypeInstance', 'cardinality' => 'oneToOne' , 'keys' => 'idInstantiationType:idTypeInstance'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idLabel;
    /**
     * 
     * @var integer 
     */
    protected $startChar;
    /**
     * 
     * @var integer 
     */
    protected $endChar;
    /**
     * 
     * @var integer 
     */
    protected $multi;
    /**
     * 
     * @var integer 
     */
    protected $idLabelType;
    /**
     * 
     * @var integer 
     */
    protected $idLayer;
    /**
     * 
     * @var integer 
     */
    protected $idInstantiationType;

    /**
     * Associations
     */
    protected $entity;
    protected $layer;
    protected $instantiationType;
    

    /**
     * Getters/Setters
     */
    public function getIdLabel() {
        return $this->idLabel;
    }

    public function setIdLabel($value) {
        $this->idLabel = $value;
    }

    public function getStartChar() {
        return $this->startChar;
    }

    public function setStartChar($value) {
        $this->startChar = $value;
    }

    public function getEndChar() {
        return $this->endChar;
    }

    public function setEndChar($value) {
        $this->endChar = $value;
    }

    public function getMulti() {
        return $this->multi;
    }

    public function setMulti($value) {
        $this->multi = $value;
    }

    public function getIdLabelType() {
        return $this->idLabelType;
    }

    public function setIdLabelType($value) {
        $this->idLabelType = $value;
    }

    public function getIdLayer() {
        return $this->idLayer;
    }

    public function setIdLayer($value) {
        $this->idLayer = $value;
    }

    public function getIdInstantiationType() {
        return $this->idInstantiationType;
    }

    public function setIdInstantiationType($value) {
        $this->idInstantiationType = $value;
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
    public function getLayer() {
        if (is_null($this->layer)){
            $this->retrieveAssociation("layer");
        }
        return  $this->layer;
    }
    /**
     *
     * @param Association $value
     */
    public function setLayer($value) {
        $this->layer = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationLayer() {
        $this->retrieveAssociation("layer");
    }
    /**
     *
     * @return Association
     */
    public function getInstantiationType() {
        if (is_null($this->instantiationType)){
            $this->retrieveAssociation("instantiationType");
        }
        return  $this->instantiationType;
    }
    /**
     *
     * @param Association $value
     */
    public function setInstantiationType($value) {
        $this->instantiationType = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationInstantiationType() {
        $this->retrieveAssociation("instantiationType");
    }

    

}
// end - wizard

?>