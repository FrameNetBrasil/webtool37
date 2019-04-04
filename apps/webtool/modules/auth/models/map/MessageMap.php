<?php

namespace fnbr\auth\models\map;

class MessageMap extends \MBusinessModel
{


    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'auth_message',
            'attributes' => array(
                'idMessage' => array('column' => 'idMessage', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'subject' => array('column' => 'subject', 'type' => 'string'),
                'body' => array('column' => 'body', 'type' => 'string'),
                'dateSent' => array('column' => 'dateSent', 'type' => 'string'),
                'idUser' => array('column' => 'idUser', 'key' => 'foreign', 'type' => 'integer'),
                'idMsgStatus' => array('column' => 'idMsgStatus', 'key' => 'foreign', 'type' => 'integer'),
            ),
            'associations' => array()
        );
    }

    /**
     *
     * @var integer
     */
    protected $idMessage;
    /**
     *
     * @var string
     */
    protected $subject;
    /**
     *
     * @var string
     */
    protected $body;
    /**
     *
     * @var string
     */
    protected $dateSent;
    /**
     *
     * @var integer
     */
    protected $idUser;
    /**
     *
     * @var integer
     */
    protected $idMsgStatus;

    /**
     * Associations
     */


    /**
     * Getters/Setters
     */
    public function getIdMessage()
    {
        return $this->idMessage;
    }

    public function setIdMessage($value)
    {
        $this->idMessage = $value;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($value)
    {
        $this->subject = $value;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($value)
    {
        $this->body = $value;
    }

    public function getDateSent()
    {
        return $this->dateSent;
    }

    public function setDateSent($value)
    {
        $this->dateSent = $value;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function setIdUser($value)
    {
        $this->idUser = $value;
    }

    public function getIdMsgStatus()
    {
        return $this->idMsgStatus;
    }

    public function setIdMsgStatus($value)
    {
        $this->idMsgStatus = $value;
    }


}
