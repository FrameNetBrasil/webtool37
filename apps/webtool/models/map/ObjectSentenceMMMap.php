<?php
namespace fnbr\models\map;

class ObjectSentenceMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'objectsentencemm',
            'attributes' => array(
                'idObjectSentenceMM' => array('column' => 'idObjectSentenceMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'startWord' => array('column' => 'startWord','type' => 'string'),
                'endWord' => array('column' => 'endWord','type' => 'integer'),
                'name' => array('column' => 'name','type' => 'string'),
                'idObjectMM' => array('column' => 'idObjectMM','type' => 'integer'),
                'idSentenceMM' => array('column' => 'idSentenceMM','type' => 'integer'),
                'idLemma' => array('column' => 'idLemma','type' => 'integer'),
                'idFrameElement' => array('column' => 'idFrameElement','type' => 'integer'),
                'idLU' => array('column' => 'idLU','type' => 'integer'),
            ),
            'associations' => array(
                'sentencemm' => array('toClass' => 'fnbr\models\SentenceMM', 'cardinality' => 'oneToOne' , 'keys' => 'idSentenceMM:idSentenceMM'),
                'objectmm' => array('toClass' => 'fnbr\models\ObjectMM', 'cardinality' => 'oneToOne' , 'keys' => 'idObjectMM:idObjectMM'),
                'lemma' => array('toClass' => 'fnbr\models\Lemma', 'cardinality' => 'oneToOne' , 'keys' => 'idLemma:idLemma'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idObjectSentenceMM;
    /**
     *
     * @var integer
     */
    protected $startWord;
    /**
     * 
     * @var integer 
     */
    protected $endWord;
    /**
     *
     * @var string
     */
    protected $name;
    /**
     *
     * @var integer
     */
    protected $idSentenceMM;
    /**
     *
     * @var integer
     */
    protected $idObjectMM;
    /**
     *
     * @var int
     */
    protected $idLemma;
    /**
     *
     * @var int
     */
    protected $idFrameElement;
    /**
     *
     * @var int
     */
    protected $idLU;

    /**
     * Getters/Setters
     */
    public function getIdObjectSentenceMM() {
        return $this->idObjectSentenceMM;
    }

    public function setIdObjectSentenceMM($value) {
        $this->idObjectSentenceMM = $value;
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

    public function getIdObjectMM() {
        return $this->idObjectMM;
    }

    public function setIdObjectMM($value) {
        $this->idObjectMM = $value;
    }

    public function getIdSentenceMM() {
        return $this->idSentenceMM;
    }

    public function setIdSentenceMM($value) {
        $this->idSentenceMM = $value;
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

}
