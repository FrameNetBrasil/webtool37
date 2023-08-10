<?php

namespace fnbr\auth\models\map;

class UserAnnotationMap extends \MBusinessModel
{
    public static function ORMMAP()
    {
        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'userannotation',
            'attributes' => array(
                'idUser' => array('column' => 'idUser','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'idUserAnnotation' => array('column' => 'idUserAnnotation',   'type' => 'integer'),
                'idSentenceStart' => array('column' => 'idSentenceStart', 'type' => 'integer'),
                'idSentenceEnd' => array('column' => 'idSentenceEnd', 'type' => 'integer'),
                'idDocument' => array('column' => 'idDocument', 'type' => 'integer')
            ),
            'associations' => array(
                'user' => array('toClass' => 'fnbr\auth\models\User', 'cardinality' => 'oneToOne', 'keys' => 'idUser:idUser'),
                'document' => array('toClass' => 'fnbr\auth\models\Document', 'cardinality' => 'oneToOne', 'keys' => 'idDocument:idDocument'),
                'sentence' => array('toClass' => 'fnbr\auth\models\Sentence', 'cardinality' => 'oneToOne', 'keys' => 'idSentence:idSentence')
            )
        );

    }

    /**
     *
     * @var integer
     */
    protected $idUserAnnotation;
    /**
     *
     * @var integer
     */
    protected $idUser;
    /**
     *
     * @var integer
     */
    protected $idSentenceStart;
    /**
     *
     * @var integer
     */
    protected $idSentenceEnd;
    /**
     *
     * @var integer
     */
    protected $idDocument;

    /**
     *
     * Associations
     */
    protected $user;
    protected $document;
    protected $sentence;


    public function getIdUserAnnotation()
    {
        return $this->idUserAnnotation;
    }
    public function setIdUserAnnotation($idUserAnnotation)
    {
        $this->idUserAnnotation = $idUserAnnotation;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }
    public function getIdSentenceStart()
    {
        return $this->idSentenceStart;
    }

    public function setIdSentenceStart($idSentenceStart)
    {
        $this->idSentenceStart = $idSentenceStart;
    }
    public function getIdSentenceEnd()
    {
        return $this->idSentenceEnd;
    }
    public function setIdSentenceEnd($idSentenceEnd)
    {
        $this->idSentenceEnd = $idSentenceEnd;
    }

    public function getIdDocument()
    {
        return $this->idDocument;
    }

    public function setIdDocument($idDocument)
    {
        $this->idDocument = $idDocument;
    }

    /**
     * @return Association
     */
    public function getUser()
    {
        if (is_null($this->user)) {
            $this->retrieveAssociation("user");
        }
        return $this->user;
    }

    /**
     * @param Association $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Association
     */
    public function getAssociationUser()
    {
        $this->retrieveAssociation("user");
    }

    /**
     * @return Association
     */
    public function getDocument()
    {
        if (is_null($this->document)) {
            $this->retrieveAssociation("document");
        }
        return $this->document;
    }

    /**
     * @param Association $document
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }

    /**
     * @return Association
     */
    public function getAssociationDocument()
    {
        $this->retrieveAssociation("document");
    }

    /**
     * @return Association
     */
    public function getSentence()
    {
        if (is_null($this->sentence)) {
            $this->retrieveAssociation("sentence");
        }
        return $this->sentence;
    }

    /**
     * @param Association $sentence
     */
    public function setSentence($sentence)
    {
        $this->sentence = $sentence;
    }

    /**
     * @return Association
     */
    public function getAssociationSentence()
    {
        $this->retrieveAssociation("sentence");
    }

}

