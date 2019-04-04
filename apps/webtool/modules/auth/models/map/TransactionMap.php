<?php

namespace fnbr\auth\models\map;

class TransactionMap extends \MBusinessModel
{


    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'auth_transaction',
            'attributes' => array(
                'idTransaction' => array('column' => 'idTransaction', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'name' => array('column' => 'name', 'type' => 'string'),
                'description' => array('column' => 'description', 'type' => 'string'),
            ),
            'associations' => array(
                'accesss' => array('toClass' => 'fnbr\auth\models\Access', 'cardinality' => 'oneToMany', 'keys' => 'idTransaction:idTransaction'),
            )
        );
    }

    /**
     *
     * @var integer
     */
    protected $idTransaction;
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
    protected $accesss;


    /**
     * Getters/Setters
     */
    public function getIdTransaction()
    {
        return $this->idTransaction;
    }

    public function setIdTransaction($value)
    {
        $this->idTransaction = $value;
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
    public function getAccesss()
    {
        if (is_null($this->accesss)) {
            $this->retrieveAssociation("accesss");
        }
        return $this->accesss;
    }

    /**
     *
     * @param Association $value
     */
    public function setAccesss($value)
    {
        $this->accesss = $value;
    }

    /**
     *
     * @return Association
     */
    public function getAssociationAccesss()
    {
        $this->retrieveAssociation("accesss");
    }


}
