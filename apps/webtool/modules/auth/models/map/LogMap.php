<?php

namespace fnbr\auth\models\map;

class LogMap extends \MBusinessModel
{


    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'auth_log',
            'attributes' => array(
                'idLog' => array('column' => 'idLog', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'ts' => array('column' => 'ts', 'type' => 'timestamp'),
                'operation' => array('column' => 'operation', 'type' => 'string'),
                'idUser' => array('column' => 'idUser', 'type' => 'integer'),
            ),
            'associations' => array(
                'user' => array('toClass' => 'fnbr\auth\models\User', 'cardinality' => 'oneToOne', 'keys' => 'idUser:idUser'),
            )
        );
    }

    /**
     *
     * @var integer
     */
    protected $idLog;
    /**
     *
     * @var timestamp
     */
    protected $ts;
    /**
     *
     * @var string
     */
    protected $operation;
    /**
     *
     * @var integer
     */
    protected $idUser;

    /**
     * Associations
     */
    protected $user;


    /**
     * Getters/Setters
     */
    public function getIdLog()
    {
        return $this->idLog;
    }

    public function setIdLog($value)
    {
        $this->idLog = $value;
    }

    public function getTs()
    {
        return $this->ts;
    }

    public function setTs($value)
    {
        if (!($value instanceof \MTimeStamp)) {
            $value = new \MTimeStamp($value);
        }
        $this->ts = $value;
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function setOperation($value)
    {
        $this->operation = $value;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function setIdUser($value)
    {
        $this->idUser = $value;
    }

    /**
     *
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
     *
     * @param Association $value
     */
    public function setUser($value)
    {
        $this->user = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAssociationUser()
    {
        $this->retrieveAssociation("user");
    }


}
