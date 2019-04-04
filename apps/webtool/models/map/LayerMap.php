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

class LayerMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'layer',
            'attributes' => array(
                'idLayer' => array('column' => 'idLayer','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'rank' => array('column' => 'rank','type' => 'integer'),
                'timeline' => array('column' => 'timeline','type' => 'string'),
                'idAnnotationSet' => array('column' => 'idAnnotationSet','type' => 'integer'),
                'idLayerType' => array('column' => 'idLayerType','type' => 'integer'),
            ),
            'associations' => array(
                'annotationset' => array('toClass' => 'fnbr\models\AnnotationSet', 'cardinality' => 'oneToOne' , 'keys' => 'idAnnotationSet:idAnnotationSet'), 
                'layertype' => array('toClass' => 'fnbr\models\LayerType', 'cardinality' => 'oneToOne' , 'keys' => 'idLayerType:idLayerType'), 
                'labels' => array('toClass' => 'fnbr\models\Label', 'cardinality' => 'oneToMany' , 'keys' => 'idLayer:idLayer'), 
                'timelines' => array('toClass' => 'fnbr\models\Timeline', 'cardinality' => 'oneToMany' , 'keys' => 'timeline:timeline'), 
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idLayer;
    /**
     * 
     * @var integer 
     */
    protected $rank;
    /**
     * 
     * @var string 
     */
    protected $timeline;
    /**
     * 
     * @var integer 
     */
    protected $idAnnotationSet;
    /**
     * 
     * @var integer 
     */
    protected $idLayerType;

    /**
     * Associations
     */
    protected $annotationset;
    protected $layertype;
    protected $labels;
    protected $timelines;
    

    /**
     * Getters/Setters
     */
    public function getIdLayer() {
        return $this->idLayer;
    }

    public function setIdLayer($value) {
        $this->idLayer = $value;
    }

    public function getRank() {
        return $this->rank;
    }

    public function setRank($value) {
        $this->rank = $value;
    }

    public function getTimeline() {
        return $this->timeline;
    }

    public function setTimeline($value) {
        $this->timeline = $value;
    }

    public function getIdAnnotationSet() {
        return $this->idAnnotationSet;
    }

    public function setIdAnnotationSet($value) {
        $this->idAnnotationSet = $value;
    }

    public function getIdLayerType() {
        return $this->idLayerType;
    }

    public function setIdLayerType($value) {
        $this->idLayerType = $value;
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
    /**
     *
     * @return Association
     */
    public function getLayertype() {
        if (is_null($this->layertype)){
            $this->retrieveAssociation("layertype");
        }
        return  $this->layertype;
    }
    /**
     *
     * @param Association $value
     */
    public function setLayertype($value) {
        $this->layertype = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationLayertype() {
        $this->retrieveAssociation("layertype");
    }
    /**
     *
     * @return Association
     */
    public function getLabels() {
        if (is_null($this->labels)){
            $this->retrieveAssociation("labels");
        }
        return  $this->labels;
    }
    /**
     *
     * @param Association $value
     */
    public function setLabels($value) {
        $this->labels = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationLabels() {
        $this->retrieveAssociation("labels");
    }
    /**
     *
     * @return Association
     */
    public function getTimelines() {
        if (is_null($this->timelines)){
            $this->retrieveAssociation("timelines");
        }
        return  $this->timelines;
    }
    /**
     *
     * @param Association $value
     */
    public function setTimelines($value) {
        $this->timelines = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationTimelines() {
        $this->retrieveAssociation("timelines");
    }

    

}
// end - wizard

?>