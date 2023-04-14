<?php
namespace fnbr\models\map;

class WordMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'wordmm',
            'attributes' => array(
                'idWordMM' => array('column' => 'idWordMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'word' => array('column' => 'word','type' => 'string'),
                'startTimestamp' => array('column' => 'startTimestamp','type' => 'string'),
                'endTimestamp' => array('column' => 'endTimestamp','type' => 'string'),
                'startTime' => array('column' => 'startTime','type' => 'float'),
                'endTime' => array('column' => 'endTime','type' => 'float'),
                'origin' => array('column' => 'origin','type' => 'integer'),
                'idDocumentMM' => array('column' => 'idDocumentMM','type' => 'integer'),
            ),
            'associations' => array(
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idWordMM;
    /**
     *
     * @var string
     */
    protected $word;
    /**
     * 
     * @var string 
     */
    protected $startTimestamp;
    /**
     * 
     * @var integer 
     */
    protected $endTimestamp;
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
     * @var string 
     */
    protected $idDocumentMM;


    /**
     * Getters/Setters
     */
    public function getIdWordMM() {
        return $this->idWordMM;
    }

    public function setIdWordMM($value) {
        $this->idWordMM = $value;
    }

    public function getWord() {
        return $this->word;
    }

    public function setWord($value) {
        $this->word = $value;
    }

    public function getStartTimestamp() {
        return $this->startTimestamp;
    }

    public function setStartTimestamp($value) {
        $this->startTimestamp = $value;
    }

    public function getEndTimestamp() {
        return $this->endTimestamp;
    }

    public function setEndTimestamp($value) {
        $this->endTimestamp = $value;
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

    public function getIdDocumentMM() {
        return $this->idDocumentMM;
    }

    public function setIdDocumentMM($value) {
        $this->idDocumentMM = $value;
    }

}

