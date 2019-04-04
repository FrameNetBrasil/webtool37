<?php

namespace fnbr\auth\models\map;

class AccessMap extends \MBusinessModel
{


    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'auth_access',
            'attributes' => array(
                'idAccess' => array('column' => 'idAccess', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'rights' => array('column' => 'rights', 'type' => 'integer'),
                'idGroup' => array('column' => 'idGroup', 'type' => 'integer'),
                'idTransaction' => array('column' => 'idTransaction', 'type' => 'integer'),
            ),
            'associations' => array(
                'group' => array('toClass' => 'fnbr\auth\models\Group', 'cardinality' => 'oneToOne', 'keys' => 'idGroup:idGroup'),
                'transaction' => array('toClass' => 'fnbr\auth\models\Transaction', 'cardinality' => 'oneToOne', 'keys' => 'idTransaction:idTransaction'),
            )
        );
    }

    /**
     *
     * @var integer
     */
    protected $idAccess;
    /**
     *
     * @var integer
     */
    protected $rights;
    /**
     *
     * @var integer
     */
    protected $idGroup;
    /**
     *
     * @var integer
     */
    protected $idTransaction;

    /**
     * Associations
     */
    protected $group;
    protected $transaction;


    /**
     * Getters/Setters
     */
    public function getIdAccess()
    {
        return $this->idAccess;
    }

    public function setIdAccess($value)
    {
        $this->idAccess = $value;
    }

    public function getRights()
    {
        return $this->rights;
    }

    public function setRights($value)
    {
        $this->rights = $value;
    }

    public function getIdGroup()
    {
        return $this->idGroup;
    }

    public function setIdGroup($value)
    {
        $this->idGroup = $value;
    }

    public function getIdTransaction()
    {
        return $this->idTransaction;
    }

    public function setIdTransaction($value)
    {
        $this->idTransaction = $value;
    }

    /**
     *
     * @return Association
     */
    public function getGroup()
    {
        if (is_null($this->group)) {
            $this->retrieveAssociation("group");
        }
        return $this->group;
    }

    /**
     *
     * @param Association $value
     */
    public function setGroup($value)
    {
        $this->group = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAssociationGroup()
    {
        $this->retrieveAssociation("group");
    }

    /**
     *
     * @return Association
     */
    public function getTransaction()
    {
        if (is_null($this->transaction)) {
            $this->retrieveAssociation("transaction");
        }
        return $this->transaction;
    }

    /**
     *
     * @param Association $value
     */
    public function setTransaction($value)
    {
        $this->transaction = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAssociationTransaction()
    {
        $this->retrieveAssociation("transaction");
    }

}
