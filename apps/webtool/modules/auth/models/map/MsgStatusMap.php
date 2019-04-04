<?php

namespace fnbr\auth\models\map;

class MsgStatusMap extends \MBusinessModel
{


    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'auth_msgstatus',
            'attributes' => array(
                'idMsgStatus' => array('column' => 'idMsgStatus', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'status' => array('column' => 'status', 'type' => 'string'),
                'description' => array('column' => 'description', 'type' => 'string'),
            ),
            'associations' => array()
        );
    }

    /**
     *
     * @var integer
     */
    protected $idMsgStatus;
    /**
     *
     * @var string
     */
    protected $status;
    /**
     *
     * @var string
     */
    protected $description;

    /**
     * Associations
     */


    /**
     * Getters/Setters
     */
    public function getIdMsgStatus()
    {
        return $this->idMsgStatus;
    }

    public function setIdMsgStatus($value)
    {
        $this->idMsgStatus = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }


}
