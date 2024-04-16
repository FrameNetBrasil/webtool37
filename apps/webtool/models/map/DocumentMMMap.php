<?php
namespace fnbr\models\map;

class DocumentMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'documentmm',
            'attributes' => array(
                'idDocumentMM' => array('column' => 'idDocumentMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'title' => array('column' => 'title','type' => 'string'),
                'originalFile' => array('column' => 'originalFile','type' => 'string'),
                'sha1Name' => array('column' => 'sha1Name','type' => 'string'),
                'audioPath' => array('column' => 'audioPath','type' => 'string'),
                'videoPath' => array('column' => 'videoPath','type' => 'string'),
                'videoWidth' => array('column' => 'videoWidth','type' => 'integer'),
                'videoHeight' => array('column' => 'videoHeight','type' => 'integer'),
                'alignPath' => array('column' => 'alignPath','type' => 'string'),
                'flickr30k' => array('column' => 'flickr30k','type' => 'string'),
                'enabled' => array('column' => 'enabled','type' => 'string'),
                'idDocument' => array('column' => 'idDocument','type' => 'integer'),
                'idLanguage' => array('column' => 'idLanguage','type' => 'integer'),
            ),
            'associations' => array(
                'objectmm' => array('toClass' => 'fnbr\models\ObjectMM', 'cardinality' => 'oneToMany' , 'keys' => 'idDocumentMM:idDocumentMM'),
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idDocumentMM;
    /**
     *
     * @var string
     */
    protected $title;
    /**
     *
     * @var string
     */
    protected $originalFile;
    /**
     *
     * @var string
     */
    protected $sha1Name;
    /**
     * 
     * @var string 
     */
    protected $audioPath;
    /**
     * 
     * @var string 
     */
    protected $videoPath;
    /**
     * 
     * @var string 
     */
    protected $alignPath;
    /**
     * 
     * @var integer 
     */
    protected $videoWidth;
    /**
     *
     * @var integer
     */
    protected $videoHeight;
    /**
     *
     * @var string
     */
    protected $flickr30k;
    /**
     *
     * @var string
     */
    protected $enabled;
    /**
     *
     * @var integer
     */
    protected $idDocument;
    /**
     *
     * @var integer
     */
    protected $idLanguage;

    /**
     * Getters/Setters
     */
    public function getIdDocumentMM() {
        return $this->idDocumentMM;
    }

    public function setIdDocumentMM($value) {
        $this->idDocumentMM = $value;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($value) {
        $this->title = $value;
    }

    public function getOriginalFile() {
        return $this->originalFile;
    }

    public function setOriginalFile($value) {
        $this->originalFile = $value;
    }

    public function getSHA1Name() {
        return $this->sha1Name;
    }

    public function setSHA1Name($value) {
        $this->sha1Name = $value;
    }

    public function getAudioPath() {
        return $this->audioPath;
    }

    public function setAudioPath($value) {
        $this->audioPath = $value;
    }

    public function getVideoPath() {
        return $this->videoPath;
    }

    public function setVideoPath($value) {
        $this->videoPath = $value;
    }

    public function getAlignPath() {
        return $this->alignPath;
    }

    public function setAlignPath($value) {
        $this->alignPath = $value;
    }

    public function getIdDocument() {
        return $this->idDocument;
    }

    public function setIdDocument($value) {
        $this->idDocument = $value;
    }

    public function getVideoWidth() {
        return $this->videoWidth;
    }

    public function setVideoWidth($value) {
        $this->videoWidth = $value;
    }

    public function getVideoHeight() {
        return $this->videoHeight;
    }

    public function setVideoHeight($value) {
        $this->videoHeight = $value;
    }

    public function getIdLanguage() {
        return $this->idLanguage;
    }

    public function setIdLanguage($value) {
        $this->idLanguage = $value;
    }

    public function getFlickr30k() {
        return $this->flickr30k;
    }

    public function setFlickr30k($value) {
        $this->flickr30k = $value;
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function setEnabled($value) {
        $this->enabled = $value;
    }
}
