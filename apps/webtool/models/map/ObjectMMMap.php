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

namespace fnbr\models\map;

class ObjectMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'objectmm',
            'attributes' => array(
                'idObjectMM' => array('column' => 'idObjectMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'fileObject' => array('column' => 'fileObject','type' => 'string'),
                'idAnnotationSetMM' => array('column' => 'idAnnotationSetMM','type' => 'integer'),
            ),
            'associations' => array(
                'idAnnotationSetMM' => array('toClass' => 'fnbr\models\AnnotationSet', 'cardinality' => 'oneToOne' , 'keys' => 'idAnnotationSetMM:idAnnotationSetMM'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idObjectMM;
    /**
     * 
     * @var string 
     */
    protected $fileObject;
    /**
     *
     * @var string
     */
    protected $idAnnotationSetMM;

    /**
     * Associations
     */
    protected $annotationSetMM;
    

    /**
     * Getters/Setters
     */
    public function getIdObjectMM() {
        return $this->idObjectMM;
    }

    public function setIdObjectMM($value) {
        $this->idObjectMM = $value;
    }

    public function getFileObject() {
        return $this->fileObject;
    }

    public function setFileObject($value) {
        $this->fileObject = $value;
    }
    public function getIdAnnotationSetMM() {
        return $this->idAnnotationSetMM;
    }

    public function setIdAnnotationSetMM($value) {
        $this->idAnnotationSetMM = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAnnotationSetMM() {
        if (is_null($this->annotationSetMM)){
            $this->retrieveAssociation("anotattionsetmm");
        }
        return  $this->annotationsetmm;
    }
    /**
     *
     * @param Association $value
     */
    public function setAnnotationSetMM($value) {
        $this->annotationsetmm = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationAnnotationSetMM() {
        $this->retrieveAssociation("annotationsetmm");
    }

}
// end - wizard