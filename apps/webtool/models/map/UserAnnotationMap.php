<?php
namespace fnbr\models\map;

class UserAnnotationMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'userannotation',
            'attributes' => array(
                'idUserAnnotation' => array('column' => 'idUserAnnotation','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'idUser' => array('column' => 'idUser','type' => 'integer'),
                'idSentenceStart' => array('column' => 'idSentenceStart','type' => 'integer'),
                'idSentenceEnd' => array('column' => 'idSentenceEnd','type' => 'integer'),
            ),
            'associations' => array(
                'user' => array('toClass' => 'fnbr\auth\models\User', 'cardinality' => 'oneToOne' , 'keys' => 'idUser:idUser'),
                'sentenceStart' => array('toClass' => 'fnbr\models\Sentence', 'cardinality' => 'oneToOne' , 'keys' => 'idSentenceStart:idSentence'),
                'sentenceEnd' => array('toClass' => 'fnbr\models\Sentence', 'cardinality' => 'oneToOne' , 'keys' => 'idSentenceEnd:idSentence'),
            )
        );
    }
    
    protected $idUserAnnotation;
    protected $idUser;
    protected $idSentenceStart;
    protected $idSentenceEnd;

    protected $user;
    protected $sentenceStart;
    protected $sentenceEnd;

    public function getIdUserAnnotation() {
        return $this->idUserAnnotation;
    }

    public function setIdUserAnnotation($value) {
        $this->idUserAnnotation = $value;
    }


    public function getIdUser() {
        return $this->idUser;
    }

    public function setIdUser($value) {
        $this->idUser = $value;
    }

    public function getIdSentenceStart() {
        return $this->idSentenceStart;
    }

    public function setIdSentenceStart($value) {
        $this->idSentenceStart = $value;
    }

    public function getIdSentenceEnd() {
        return $this->idSentenceEnd;
    }

    public function setIdSentenceEnd($value) {
        $this->idSentenceEnd = $value;
    }

    public function getUser() {
        if (is_null($this->user)){
            $this->retrieveAssociation("user");
        }
        return  $this->user;
    }
    public function setUser($value) {
        $this->user = $value;
    }
    public function getAssociationUser() {
        $this->retrieveAssociation("user");
    }


    public function getSentenceStart() {
        if (is_null($this->sentenceStart)){
            $this->retrieveAssociation("sentenceStart");
        }
        return  $this->sentenceStart;
    }
    public function setSentenceStart($value) {
        $this->sentenceStart = $value;
    }
    public function getAssociationSentenceStart() {
        $this->retrieveAssociation("sentenceStart");
    }

    public function getSentenceEnd() {
        if (is_null($this->sentenceEnd)){
            $this->retrieveAssociation("sentenceEnd");
        }
        return  $this->sentenceEnd;
    }
    public function setSentenceEnd($value) {
        $this->sentenceEnd = $value;
    }
    public function getAssociationSentenceEnd() {
        $this->retrieveAssociation("sentenceEnd");
    }

}
// end - wizard