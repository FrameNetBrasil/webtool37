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

class SubCorpusMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'subcorpus',
            'attributes' => array(
                'idSubCorpus' => array('column' => 'idSubCorpus','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
                'rank' => array('column' => 'rank','type' => 'integer'),
                'idEntity' => array('column' => 'idEntity','type' => 'integer'),
            ),
            'associations' => array(
                'entity' => array('toClass' => 'fnbr\models\Entity', 'cardinality' => 'oneToOne' , 'keys' => 'idEntity:idEntity'), 
                'annotationsets' => array('toClass' => 'fnbr\models\AnnotationSet', 'cardinality' => 'oneToMany' , 'keys' => 'idSubCorpus:idSubCorpus'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idSubCorpus;
    /**
     * 
     * @var string 
     */
    protected $name;
    /**
     * 
     * @var integer 
     */
    protected $rank;
    /**
     * 
     * @var integer 
     */
    protected $idEntity;

    /**
     * Associations
     */
    protected $entity;
    protected $annotationsets;
    

    /**
     * Getters/Setters
     */
    public function getIdSubCorpus() {
        return $this->idSubCorpus;
    }

    public function setIdSubCorpus($value) {
        $this->idSubCorpus = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
    }

    public function getRank() {
        return $this->rank;
    }

    public function setRank($value) {
        $this->rank = $value;
    }

    public function getIdEntity() {
        return $this->idEntity;
    }

    public function setIdEntity($value) {
        $this->idEntity = $value;
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
    public function getAnnotationsets() {
        if (is_null($this->annotationsets)){
            $this->retrieveAssociation("annotationsets");
        }
        return  $this->annotationsets;
    }
    /**
     *
     * @param Association $value
     */
    public function setAnnotationsets($value) {
        $this->annotationsets = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationAnnotationsets() {
        $this->retrieveAssociation("annotationsets");
    }
}
// end - wizard

?>