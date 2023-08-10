<?php

namespace fnbr\models\map;

class StatusMMMap extends \MBusinessModel
{
    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'statusmm',
            'attributes' => array(
                'idStatusMM' => array('column' => 'idStatusMM', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'file' => array('column' => 'file', 'type' => 'integer'),
                'video' => array('column' => 'video', 'type' => 'integer'),
                'audio' => array('column' => 'audio', 'type' => 'integer'),
                'speechToText' => array('column' => 'speechToText', 'type' => 'integer'),
                'frames' => array('column' => 'frames', 'type' => 'integer'),
                'yolo' => array('column' => 'yolo', 'type' => 'integer'),
                'idDocumentMM' => array('column' => 'idDocumentMM', 'type' => 'integer'),
            ),
            'associations' => array(
                'documentmm' => array('toClass' => 'fnbr\models\DocumentMM', 'cardinality' => 'oneToOne', 'keys' => 'idDocumentMM:idDocumentMM'),
            )
        );
    }

    /**
     *
     * @var integer
     */
    protected $idStatusMM;
    /**
     *
     * @var integer
     */
    protected $file;

    /**
     *
     * @var integer
     */
    protected $video;
    /**
     *
     * @var integer
     */
    protected $audio;
    /**
     *
     * @var integer
     */
    protected $speechToText;
    /**
     *
     * @var integer
     */
    protected $frames;
    /**
     *
     * @var integer
     */
    protected $yolo;
    /**
     *
     * @var integer
     */
    protected $idDocumentMM;

    /**
     * Getters/Setters
     */
    public function getIdStatusMM()
    {
        return $this->idStatusMM;
    }

    public function setIdStatusMM($value)
    {
        $this->idStatusMM = $value;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($value)
    {
        $this->file = $value;
    }

    public function getVideo()
    {
        return $this->video;
    }

    public function setVideo($value)
    {
        $this->video = $value;
    }

    public function getAudio()
    {
        return $this->audio;
    }

    public function setAudio($value)
    {
        $this->audio = $value;
    }

    public function getSpeechToText()
    {
        return $this->speechToText;
    }

    public function setSpeechToText($value)
    {
        $this->speechToText = $value;
    }

    public function getFrames()
    {
        return $this->frames;
    }

    public function setFrames($value)
    {
        $this->frames = $value;
    }

    public function getYolo()
    {
        return $this->yolo;
    }

    public function setYolo($value)
    {
        $this->yolo = $value;
    }

    public function getIdDocumentMM()
    {
        return $this->idDocumentMM;
    }

    public function setIdDocumentMM($value)
    {
        $this->idDocumentMM = $value;
    }

    /**
     *
     * @return Association
     */
    public function getDocumentMM()
    {
        if (is_null($this->documentmm)) {
            $this->retrieveAssociation("documentmm");
        }
        return $this->documentmm;
    }

    /**
     *
     * @param Association $value
     */
    public function setDocumentmm($value)
    {
        $this->documentmm = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAssociationDocumentMM()
    {
        $this->retrieveAssociation("documentmm");
    }


}
