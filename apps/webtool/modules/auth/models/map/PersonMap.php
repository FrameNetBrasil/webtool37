<?php

namespace fnbr\auth\models\map;

class PersonMap extends \MBusinessModel
{


    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'auth_person',
            'attributes' => array(
                'idPerson' => array('column' => 'idPerson', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'name' => array('column' => 'name', 'type' => 'string'),
                'email' => array('column' => 'email', 'type' => 'string'),
                'nick' => array('column' => 'nick', 'type' => 'string'),
                'auth0IdUser' => array('column' => 'auth0IdUser', 'type' => 'string'),
                'auth0CreatedAt' => array('column' => 'auth0CreatedAt', 'type' => 'string'),
            ),
            'associations' => array(
                'users' => array('toClass' => 'fnbr\auth\models\User', 'cardinality' => 'oneToMany', 'keys' => 'idPerson:idPerson'),
            )
        );
    }

    /**
     *
     * @var integer
     */
    protected $idPerson;
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
     * Associations
     */
    protected $users;


    /**
     * Getters/Setters
     */
    public function getIdPerson()
    {
        return $this->idPerson;
    }

    public function setIdPerson($value)
    {
        $this->idPerson = $value;
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
    /**
     *
     * @return Association
     */
    public function getUsers()
    {
        if (is_null($this->users)) {
            $this->retrieveAssociation("users");
        }
        return $this->users;
    }

    /**
     *
     * @param Association $value
     */
    public function setUsers($value)
    {
        $this->users = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAssociationUsers()
    {
        $this->retrieveAssociation("users");
    }


}
