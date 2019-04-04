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

class ASCommentsMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'ascomments',
            'attributes' => array(
                'idASComments' => array('column' => 'idASComments','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'extraThematicFE' => array('column' => 'extraThematicFE','type' => 'string'),
                'extraThematicFEOther' => array('column' => 'extraThematicFEOther','type' => 'string'),
                'comment' => array('column' => 'comment','type' => 'string'),
                'construction' => array('column' => 'construction','type' => 'string'),
                'idAnnotationSet' => array('column' => 'idAnnotationSet','key' => 'foreign','type' => 'integer')
            ),
            'associations' => array(
                'annotationset' => array('toClass' => 'fnbr\models\AnnotationSet', 'cardinality' => 'oneToOne' , 'keys' => 'idAnnotationSet:idAnnotationSet')
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idASComments;
    /**
     * 
     * @var string
     */
    protected $extraThematicFE;
    /**
     *
     * @var string
     */
    protected $extraThematicFEOther;
    /**
     * 
     * @var string 
     */
    protected $comment;
    /**
     * 
     * @var string
     */
    protected $construction;
    /**
     * 
     * @var integer 
     */
    protected $idAnnotationSet;

    /**
     * Associations
     */
    protected $annotationset;


    /**
     * Getters/Setters
     */
    public function getIdASComments() {
        return $this->idASComments;
    }

    public function setIdASComments($value) {
        $this->idASComments = $value;
    }

    public function getExtraThematicFE() {
        return $this->extraThematicFE;
    }

    public function setExtraThematicFE($value) {
        $this->extraThematicFE = $value;
    }

    public function getExtraThematicFEOther() {
        return $this->extraThematicFEOther;
    }

    public function setExtraThematicFEOther($value) {
        $this->extraThematicFEOther = $value;
    }

    public function getComment() {
        return $this->comment;
    }

    public function setComment($value) {
        $this->comment = $value;
    }

    public function getConstruction() {
        return $this->construction;
    }

    public function setConstruction($value) {
        $this->construction = $value;
    }

    public function getIdAnnotationSet() {
        return $this->idAnnotationSet;
    }

    public function setIdAnnotationSet($value) {
        $this->idAnnotationSet = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAnnotationset() {
        if (is_null($this->annotationset)){
            $this->retrieveAssociation("annotationset");
        }
        return  $this->annotationset;
    }
    /**
     *
     * @param Association $value
     */
    public function setAnnotationset($value) {
        $this->annotationset = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationAnnotationset() {
        $this->retrieveAssociation("annotationset");
    }


}
// end - wizard

?>