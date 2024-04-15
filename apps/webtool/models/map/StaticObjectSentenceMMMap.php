<?php
namespace fnbr\models\map;

class StaticObjectSentenceMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'staticobjectsentencemm',
            'attributes' => array(
                'idStaticObjectSentenceMM' => array('column' => 'idStaticObjectSentenceMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'startWord' => array('column' => 'startWord','type' => 'string'),
                'endWord' => array('column' => 'endWord','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
                'idStaticObjectMM' => array('column' => 'idStaticObjectMM','type' => 'integer'),
                'idStaticSentenceMM' => array('column' => 'idStaticSentenceMM','type' => 'integer'),
            ),
            'associations' => array(
                'staticsentencemm' => array('toClass' => 'fnbr\models\StaticSentenceMM', 'cardinality' => 'oneToOne' , 'keys' => 'idStaticSentenceMM:idStaticSentenceMM'),
                'staticobjectmm' => array('toClass' => 'fnbr\models\StaticObjectMM', 'cardinality' => 'oneToOne' , 'keys' => 'idStaticObjectMM:idStaticObjectMM'),
                'staticannotationmm' => array('toClass' => 'fnbr\models\StaticAnnotationMM', 'cardinality' => 'oneToOne' , 'keys' => 'idStaticObjectSentenceMM:idStaticObjectSentenceMM'),
            )
        );
    }
    
    protected $idStaticObjectSentenceMM;
    protected $startWord;
    protected $endWord;
    protected $name;
    protected $idStaticSentenceMM;
    protected $idStaticObjectMM;

    /**
     * Getters/Setters
     */
    public function getIdStaticObjectSentenceMM() {
        return $this->idStaticObjectSentenceMM;
    }

    public function setIdStaticObjectSentenceMM($value) {
        $this->idStaticObjectSentenceMM = $value;
    }

    public function getStartWord() {
        return $this->startWord;
    }

    public function setStartWord($value) {
        $this->startWord = $value;
    }

    public function getEndWord() {
        return $this->endWord;
    }

    public function setEndWord($value) {
        $this->endWord = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
    }

    public function getIdStaticObjectMM() {
        return $this->idStaticObjectMM;
    }

    public function setIdStaticObjectMM($value) {
        $this->idStaticObjectMM = $value;
    }

    public function getIdStaticSentenceMM() {
        return $this->idStaticSentenceMM;
    }

    public function setIdStaticSentenceMM($value) {
        $this->idStaticSentenceMM = $value;
    }

}
