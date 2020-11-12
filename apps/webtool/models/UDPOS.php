<?php

namespace fnbr\models;

class UDPOS extends map\UDPOSMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'POS' => array('notnull'),
                'entry' => array('notnull'),
                'idEntity' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription()
    {
        return $this->getPOS();
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('POS');
        if ($filter->POS) {
            $criteria->where("POS LIKE '{$filter->POS}%'");
        }
        return $criteria;
    }

    public function listForLookup($type)
    {
        $whereType = ($type == '*') ? '' : "WHERE (t.entry = '{$type}')";
        $cmd = <<<HERE
        SELECT u.idUDPOS, u.POS
        FROM UDPOS u
        {$whereType} 
        ORDER BY u.POS

HERE;
        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function listForLookupEntity($type)
    {
        $whereType = ($type == '*') ? '' : "WHERE (t.entry = '{$type}')";
        $cmd = <<<HERE
        SELECT u.idUDPOS, u.POS, u.idEntity
        FROM UDPOS u
        {$whereType} 
        ORDER BY u.POS

HERE;
        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

}
