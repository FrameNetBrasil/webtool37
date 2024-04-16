<?php
namespace fnbr\models\map;

class DynamicSentenceMMMap extends \MBusinessModel {


    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'dynamicsentencemm',
            'attributes' => array(
                'idDynamicSentenceMM' => array('column' => 'idDynamicSentenceMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'startTime' => array('column' => 'startTime','type' => 'float'),
                'endTime' => array('column' => 'endTime','type' => 'float'),
                'origin' => array('column' => 'origin','type' => 'integer'),
                'idSentence' => array('column' => 'idSentence','type' => 'integer'),
                'idOriginMM' => array('column' => 'idOriginMM','type' => 'integer'),
            ),
            'associations' => array(
                'sentence' => array('toClass' => 'fnbr\models\Sentence', 'cardinality' => 'oneToOne' , 'keys' => 'idSentence:idSentence'),
                'originmm' => array('toClass' => 'fnbr\models\OriginMM', 'cardinality' => 'oneToOne' , 'keys' => 'idOriginMM:idOriginMM'),
            )
        );
    }

    /**
     *
     * @var integer
     */
    protected $idDynamicSentenceMM;
    /**
     *
     * @var float
     */
    protected $startTime;
    /**
     *
     * @var float
     */
    protected $endTime;
    /**
     *
     * @var integer
     */
    protected $origin;
    /**
     *
     * @var integer
     */
    protected $idSentence;
    /**
     * Associations
     */
    protected $sentence;


    /**
     * Getters/Setters
     */
    public function getIdDynamicSentenceMM() {
        return $this->idDynamicSentenceMM;
    }

    public function setIdDynamicSentenceMM($value) {
        $this->idDynamicSentenceMM = $value;
    }

    public function getStartTime() {
        return $this->startTime;
    }

    public function setStartTime($value) {
        $this->startTime = $value;
    }

    public function getEndTime() {
        return $this->endTime;
    }

    public function setEndTime($value) {
        $this->endTime = $value;
    }

    public function getOrigin() {
        return $this->origin;
    }

    public function setOrigin($value) {
        $this->origin = $value;
    }

    public function getIdSentence() {
        return $this->idSentence;
    }

    public function setIdSentence($value) {
        $this->idSentence = $value;
    }

    public function getIdOriginMM() {
        return $this->idOriginMM;
    }

    public function setIdOriginMM($value) {
        $this->idOriginMM = $value;
    }
    /**
     *
     * @return Association
     */
    public function getSentence() {
        if (is_null($this->sentence)){
            $this->retrieveAssociation("sentence");
        }
        return  $this->sentence;
    }
    /**
     *
     * @param Association $value
     */
    public function setSentence($value) {
        $this->sentence = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationSentence() {
        $this->retrieveAssociation("sentence");
    }

}


