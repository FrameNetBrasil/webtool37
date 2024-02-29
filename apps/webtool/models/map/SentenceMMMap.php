<?php
namespace fnbr\models\map;

class SentenceMMMap extends \MBusinessModel {


    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'sentencemm',
            'attributes' => array(
                'idSentenceMM' => array('column' => 'idSentenceMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'startTimestamp' => array('column' => 'startTimestamp','type' => 'string'),
                'endTimestamp' => array('column' => 'endTimestamp','type' => 'string'),
                'startTime' => array('column' => 'startTime','type' => 'float'),
                'endTime' => array('column' => 'endTime','type' => 'float'),
                'origin' => array('column' => 'origin','type' => 'integer'),
                'idFlickr30k' => array('column' => 'idFlickr30k','type' => 'integer'),
                'idSentence' => array('column' => 'idSentence','type' => 'integer'),
                'idImageMM' => array('column' => 'idImageMM','type' => 'integer'),
                'idDocumentMM' => array('column' => 'idDocumentMM','type' => 'integer'),
                'idOriginMM' => array('column' => 'idOriginMM','type' => 'integer'),
            ),
            'associations' => array(
                'sentence' => array('toClass' => 'fnbr\models\Sentence', 'cardinality' => 'oneToOne' , 'keys' => 'idSentence:idSentence'),
                'imagemm' => array('toClass' => 'fnbr\models\ImageMM', 'cardinality' => 'oneToOne' , 'keys' => 'idImagemMM:idImagemMM'),
                'documentmm' => array('toClass' => 'fnbr\models\DocumentMM', 'cardinality' => 'oneToOne' , 'keys' => 'idDocumentMM:idDocumentMM'),
                'originmm' => array('toClass' => 'fnbr\models\OriginMM', 'cardinality' => 'oneToOne' , 'keys' => 'idOriginMM:idOriginMM'),
                'objectsentencemm' => array('toClass' => 'fnbr\models\ObjectSentenceMM', 'cardinality' => 'oneToMany' , 'keys' => 'idSentenceMM:idSentenceMM'),
            )
        );
    }

    /**
     *
     * @var integer
     */
    protected $idSentenceMM;
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
     * @var integer
     */
    protected $idSentence;
    /**
     *
     * @var integer
     */
    protected $idImageMM;
    /**
     *
     * @var integer
     */
    protected $idDocumentMM;
    protected $idFlick30k;

    /**
     * Associations
     */
    protected $sentence;
    protected $documentmm;


    /**
     * Getters/Setters
     */
    public function getIdSentenceMM() {
        return $this->idSentenceMM;
    }

    public function setIdSentenceMM($value) {
        $this->idSentenceMM = $value;
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

    public function getIdSentence() {
        return $this->idSentence;
    }

    public function setIdSentence($value) {
        $this->idSentence = $value;
    }

    public function getIdImageMM() {
        return $this->idImageMM;
    }

    public function setIdImageMM($value) {
        $this->idImageMM = $value;
    }
    public function getIdDocumentMM() {
        return $this->idDocumentMM;
    }

    public function setIdDocumenTMM($value) {
        $this->idDocumentMM = $value;
    }
    public function getIdOriginMM() {
        return $this->idOriginMM;
    }

    public function setIdOriginMM($value) {
        $this->idOriginMM = $value;
    }
    public function getIdFlickr30k() {
        return $this->idFlick30k;
    }

    public function setIdFlickr30k($value) {
        $this->idFlick30k = $value;
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

    /**
     *
     * @return Association
     */
    public function getDocumentMM() {
        if (is_null($this->documentmm)){
            $this->retrieveAssociation("documentmm");
        }
        return  $this->documentmm;
    }
    /**
     *
     * @param Association $value
     */
    public function setDocumentmm($value) {
        $this->documentmm = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationDocumentMM() {
        $this->retrieveAssociation("documentmm");
    }

}


