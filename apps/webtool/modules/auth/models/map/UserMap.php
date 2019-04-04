<?php

namespace fnbr\auth\models\map;

class UserMap extends \MBusinessModel
{


    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'auth_user',
            'attributes' => array(
                'idUser' => array('column' => 'idUser', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'login' => array('column' => 'login', 'type' => 'string'),
                'pwd' => array('column' => 'pwd', 'type' => 'string'),
                'passMD5' => array('column' => 'passMD5', 'type' => 'string'),
                'theme' => array('column' => 'theme', 'type' => 'string'),
                'config' => array('column' => 'config', 'type' => 'string'),
                'active' => array('column' => 'active', 'type' => 'integer'),
                'status' => array('column' => 'status', 'type' => 'string'),
                'name' => array('column' => 'name', 'type' => 'string'),
                'email' => array('column' => 'email', 'type' => 'string'),
                'nick' => array('column' => 'nick', 'type' => 'string'),
                'auth0IdUser' => array('column' => 'auth0IdUser', 'type' => 'string'),
                'auth0CreatedAt' => array('column' => 'auth0CreatedAt', 'type' => 'string'),
                'lastLogin' => array('column' => 'lastLogin', 'type' => 'timestamp'),
                'idPerson' => array('column' => 'idPerson', 'type' => 'integer'),
            ),
            'associations' => array(
//                'person' => array('toClass' => 'fnbr\auth\models\Person', 'cardinality' => 'oneToOne', 'keys' => 'idPerson:idPerson'),
                'logs' => array('toClass' => 'fnbr\auth\models\Log', 'cardinality' => 'oneToMany', 'keys' => 'idUser:idUser'),
                'groups' => array('toClass' => 'fnbr\auth\models\Group', 'cardinality' => 'manyToMany', 'associative' => 'auth_user_group'),
            )
        );
    }

    /**
     *
     * @var integer
     */
    protected $idUser;
    /**
     *
     * @var string
     */
    protected $login;
    /**
     *
     * @var string
     */
    protected $pwd;
    /**
     *
     * @var string
     */
    protected $passMD5;
    /**
     *
     * @var string
     */
    protected $theme;
    /**
     *
     * @var integer
     */
    protected $active;
    /**
     *
     * @var string
     */
    protected $status;
    /**
     *
     * @var string
     */
    protected $name;
    /**
     *
     * @var string
     */
    protected $email;
    /**
     *
     * @var string
     */
    protected $nick;

    /**
     *
     * @var string
     */
    protected $auth0IdUser;

    /**
     *
     * @var string
     */
    protected $auth0CreatedAt;

    /**
     *
     * @var timestamp
     */
    protected $lastLogin;
    /**
     *
     * @var integer
     */
    protected $idPerson;

    /**
     * Associations
     */
    protected $person;
    protected $logs;
    protected $groups;


    /**
     * Getters/Setters
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    public function setIdUser($value)
    {
        $this->idUser = $value;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($value)
    {
        $this->login = $value;
    }

    public function getPwd()
    {
        return $this->pwd;
    }

    public function setPwd($value)
    {
        $this->pwd = $value;
    }

    public function getPassMD5()
    {
        return $this->passMD5;
    }

    public function setPassMD5($value)
    {
        $this->passMD5 = $value;
    }

    public function getTheme()
    {
        return $this->theme;
    }

    public function setTheme($value)
    {
        $this->theme = $value;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($value)
    {
        $this->config = $value;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($value)
    {
        $this->active = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getNick()
    {
        return $this->nick;
    }

    public function setNick($value)
    {
        $this->nick = $value;
    }

    public function getAuth0IdUser()
    {
        return $this->auth0IdUser;
    }

    public function setAuth0IdUser($value)
    {
        $this->auth0IdUser = $value;
    }

    public function getAuth0CreatedAt()
    {
        return $this->auth0CreatedAt;
    }

    public function setAuth0CreatedAt($value)
    {
        $this->auth0CreatedAt = $value;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function setLastLogin($value)
    {
        if (!($value instanceof \MTimeStamp)) {
            $value = new \MTimeStamp($value);
        }
        $this->lastLogin = $value;
    }

    public function getIdPerson()
    {
        return $this->idPerson;
    }

    public function setIdPerson($value)
    {
        $this->idPerson = $value;
    }

    /**
     *
     * @return Association
     */
    public function getPerson()
    {
        if (is_null($this->person)) {
            $this->retrieveAssociation("person");
        }
        return $this->person;
    }

    /**
     *
     * @param Association $value
     */
    public function setPerson($value)
    {
        $this->person = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAssociationPerson()
    {
        $this->retrieveAssociation("person");
    }

    /**
     *
     * @return Association
     */
    public function getLogs()
    {
        if (is_null($this->logs)) {
            $this->retrieveAssociation("logs");
        }
        return $this->logs;
    }

    /**
     *
     * @param Association $value
     */
    public function setLogs($value)
    {
        $this->logs = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAssociationLogs()
    {
        $this->retrieveAssociation("logs");
    }

    /**
     *
     * @return Association
     */
    public function getGroups()
    {
        if (is_null($this->groups)) {
            $this->retrieveAssociation("groups");
        }
        return $this->groups;
    }

    /**
     *
     * @param Association $value
     */
    public function setGroups($value)
    {
        $this->groups = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAssociationGroups()
    {
        $this->retrieveAssociation("groups");
    }


}
