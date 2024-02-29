<?php
namespace fnbr\models\map;

class StaticSentenceMMMap extends \MBusinessModel {


    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'staticsentencemm',
            'attributes' => array(
                'idStaticSentenceMM' => array('column' => 'idStaticSentenceMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'idFlickr30k' => array('column' => 'idFlickr30k','type' => 'integer'),
                'idSentence' => array('column' => 'idSentence','type' => 'integer'),
                'idImageMM' => array('column' => 'idImageMM','type' => 'integer'),
                'idDocument' => array('column' => 'idDocument','type' => 'integer'),
            ),
            'associations' => array(
                'sentence' => array('toClass' => 'fnbr\models\Sentence', 'cardinality' => 'oneToOne' , 'keys' => 'idSentence:idSentence'),
                'imagemm' => array('toClass' => 'fnbr\models\ImageMM', 'cardinality' => 'oneToOne' , 'keys' => 'idImagemMM:idImagemMM'),
                'document' => array('toClass' => 'fnbr\models\Document', 'cardinality' => 'oneToOne' , 'keys' => 'idDocument:idDocument'),
                'staticobjectsentencemm' => array('toClass' => 'fnbr\models\StaticObjectSentenceMM', 'cardinality' => 'oneToMany' , 'keys' => 'idStaticSentenceMM:idStaticSentenceMM'),
            )
        );
    }

    protected $idStaticSentenceMM;
    protected $idSentence;
    protected $idImageMM;
    protected $idDocument;
    protected $idFlick30k;

    /**
     * Associations
     */
    protected $sentence;
    protected $document;


    /**
     * Getters/Setters
     */
    public function getIdStaticSentenceMM() {
        return $this->idStaticSentenceMM;
    }

    public function setIdStaticSentenceMM($value) {
        $this->idStaticSentenceMM = $value;
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
    public function getIdDocument() {
        return $this->idDocument;
    }

    public function setIdDocument($value) {
        $this->idDocument = $value;
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
    public function getDocument() {
        if (is_null($this->document)){
            $this->retrieveAssociation("document");
        }
        return  $this->document;
    }
    /**
     *
     * @param Association $value
     */
    public function setDocument($value) {
        $this->document = $value;
    }
    /**
     *
     * @return Association
     */
    public function getAssociationDocument() {
        $this->retrieveAssociation("document");
    }

}


