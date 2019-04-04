<?php

namespace fnbr\auth\models\map;

class GroupMap extends \MBusinessModel
{

    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'auth_group',
            'attributes' => array(
                'idGroup' => array('column' => 'idGroup', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'name' => array('column' => 'name', 'type' => 'string'),
                'description' => array('column' => 'description', 'type' => 'string'),
            ),
            'associations' => array(
                'access' => array('toClass' => 'fnbr\auth\models\Access', 'cardinality' => 'oneToMany', 'keys' => 'idGroup:idGroup'),
                'users' => array('toClass' => 'fnbr\auth\models\User', 'cardinality' => 'manyToMany', 'associative' => 'auth_user_group'),
            )
        );
    }

    /**
     *
     * @var integer
     */
    protected $idGroup;
    /**
     *
     * @var string
     */
    protected $name;
    /**
     *
     * @var string
     */
    protected $description;

    /**
     * Associations
     */
    protected $access;
    protected $users;


    /**
     * Getters/Setters
     */
    public function getIdGroup()
    {
        return $this->idGroup;
    }

    public function setIdGroup($value)
    {
        $this->idGroup = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAccess()
    {
        if (is_null($this->access)) {
            $this->retrieveAssociation("access");
        }
        return $this->access;
    }

    /**
     *
     * @param Association $value
     */
    public function setAccess($value)
    {
        $this->access = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAssociationAccess()
    {
        $this->retrieveAssociation("access");
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