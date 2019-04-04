<?php

namespace fnbr\auth\models\map;

class MessageBoxMap extends \MBusinessModel
{


    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'auth_messagebox',
            'attributes' => array(
                'idMessageBox' => array('column' => 'idMessageBox', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'idUser' => array('column' => 'idUser', 'key' => 'foreign', 'type' => 'integer'),
                'idMsgStatus' => array('column' => 'idMsgStatus', 'key' => 'foreign', 'type' => 'integer'),
                'idMessage' => array('column' => 'idMessage', 'key' => 'foreign', 'type' => 'integer'),
            ),
            'associations' => array()
        );
    }

    /**
     *
     * @var integer
     */
    protected $idMessageBox;
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
     *
     * @var integer
     */
    protected $idMessage;

    /**
     * Associations
     */


    /**
     * Getters/Setters
     */
    public function getIdMessageBox()
    {
        return $this->idMessageBox;
    }

    public function setIdMessageBox($value)
    {
        $this->idMessageBox = $value;
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

    public function getIdMessage()
    {
        return $this->idMessage;
    }

    public function setIdMessage($value)
    {
        $this->idMessage = $value;
    }


}
