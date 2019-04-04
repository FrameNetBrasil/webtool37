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

class GenericLabelMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'genericlabel',
            'attributes' => array(
                'idGenericLabel' => array('column' => 'idGenericLabel','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
                'definition' => array('column' => 'definition','type' => 'string'),
                'example' => array('column' => 'example','type' => 'string'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
                'idColor' => array('column' => 'idColor','type' => 'integer'),
                'idLanguage' => array('column' => 'idLanguage','type' => 'integer'),
            ),
            'associations' => array(
                'entity' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity:idEntity'), 
                'color' => array('toClass' => 'fnbr\models\Color', 'cardinality' => 'oneToOne' , 'keys' => 'idColor:idColor'), 
                'language' => array('toClass' => 'fnbr\models\Language', 'cardinality' => 'oneToOne' , 'keys' => 'idLanguage:idLanguage'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idGenericLabel;
    /**
     * 
     * @var string 
     */
    protected $name;
    /**
     * 
     * @var string 
     */
    protected $definition;
    /**
     *
     * @var string
     */
    protected $example;
    /**
     * 
     * @var integer 
     */
    protected $idEntity;
    /**
     * 
     * @var integer 
     */
    protected $idColor;
    /**
     *
     * @var integer
     */
    protected $idLanguage;

    /**
     * Associations
     */
    protected $entity;
    protected $color;
    

    /**
     * Getters/Setters
     */
    public function getIdGenericLabel() {
        return $this->idGenericLabel;
    }

    public function setIdGenericLabel($value) {
        $this->idGenericLabel = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
    }

    public function getDefinition() {
        return $this->definition;
    }

    public function setDefinition($value) {
        $this->definition = $value;
    }

    public function getExample() {
        return $this->example;
    }

    public function setExample($value) {
        $this->example = $value;
    }

    public function getIdEntity() {
        return $this->idEntity;
    }

    public function setIdEntity($value) {
        $this->idEntity = $value;
    }

    public function getIdColor() {
        return $this->idColor;
    }

    public function setIdColor($value) {
        $this->idColor = $value;
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
    public function getColor() {
        if (is_null($this->color)){
            $this->retrieveAssociation("color");
        }
        return  $this->color;
    }
    /**
     *
     * @param Association $value
     */
    public function setColor($value) {
        $this->color = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationColor() {
        $this->retrieveAssociation("color");
    }

}
// end - wizard

?>