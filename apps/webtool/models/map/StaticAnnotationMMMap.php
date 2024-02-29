<?php
namespace fnbr\models\map;

class StaticAnnotationMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'staticannotationmm',
            'attributes' => array(
                'idStaticAnnotationMM' => array('column' => 'idStaticAnnotationMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'idLemma' => array('column' => 'idLemma','type' => 'integer'),
                'idFrameElement' => array('column' => 'idFrameElement','type' => 'integer'),
                'idLU' => array('column' => 'idLU','type' => 'integer'),
                'idFrame' => array('column' => 'idFrame','type' => 'integer'),
                'idStaticObjectSentenceMM' => array('column' => 'idStaticObjectSentenceMM','type' => 'integer'),
            ),
            'associations' => array(
                'staticobjectsentencemm' => array('toClass' => 'fnbr\models\StaticObjectSentenceMM', 'cardinality' => 'oneToOne' , 'keys' => 'idStaticObjectSentenceMM:idStaticObjectSentenceMM'),
                'lemma' => array('toClass' => 'fnbr\models\Lemma', 'cardinality' => 'oneToOne' , 'keys' => 'idLemma:idLemma'),
                'lu' => array('toClass' => 'fnbr\models\LU', 'cardinality' => 'oneToOne' , 'keys' => 'idLU:idLU'),
                'frameelement' => array('toClass' => 'fnbr\models\FrameElement', 'cardinality' => 'oneToOne' , 'keys' => 'idFrameElement:idFrameElement'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idStaticAnnotationMM;
    protected $idStaticObjectSentenceMM;
    protected $idLemma;
    protected $idFrameElement;
    protected $idLU;
    protected $idFrame;

    /**
     * Getters/Setters
     */
    public function getidStaticAnnotationMM() {
        return $this->idStaticAnnotationMM;
    }

    public function setidStaticAnnotationMM($value) {
        $this->idStaticAnnotationMM = $value;
    }

    public function getidStaticObjectSentenceMM() {
        return $this->idStaticObjectSentenceMM;
    }

    public function setidStaticObjectSentenceMM($value) {
        $this->idStaticObjectSentenceMM = $value;
    }
    public function getIdLemma() {
        return $this->idLemma;
    }

    public function setIdLemma($value) {
        $this->idLemma = $value;
    }

    public function getIdFrameElement() {
        return $this->idFrameElement;
    }

    public function setIdFrameElement($value) {
        $this->idFrameElement = $value;
    }

    public function getIdLU() {
        return $this->idLU;
    }

    public function setIdLU($value) {
        $this->idLU = $value;
    }

    public function getIdFrame() {
        return $this->idFrame;
    }

    public function setIdFrame($value) {
        $this->idFrame = $value;
    }

}
