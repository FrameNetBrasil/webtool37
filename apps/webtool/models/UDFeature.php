<?php
/**
 *
 *
 * @category   Maestro
 * @package    UFJF
 * @subpackage fnbr
 * @copyright  Copyright (c) 2003-2012 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version
 * @since
 */

namespace fnbr\models;

class UDFeature extends map\UDFeatureMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'info' => array('notnull'),
                'idEntity' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription()
    {
        return $this->getInfo();
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('idTypeInstance');
        if ($filter->idTypeInstance) {
            $criteria->where("idTypeInstance LIKE '{$filter->idTypeInstance}%'");
        }
        return $criteria;
    }

    public function listForLookup($type)
    {
        $whereType = ($type == '*') ? '' : "WHERE (t.entry = '{$type}')";
        $cmd = <<<HERE
        SELECT u.idUDRelation, u.info
        FROM UDRelation u
        JOIN TypeInstance t on (u.idTypeInstance = t.idTypeInstance)
        {$whereType} 
        ORDER BY u.info

HERE;
        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

    public function listForLookupEntity($type)
    {
        $whereType = ($type == '*') ? '' : "WHERE (t.entry = '{$type}')";
        $cmd = <<<HERE
        SELECT u.idEntity, concat(t.info, ' - ', u.info) as info
        FROM UDFeature u
        JOIN TypeInstance t on (u.idTypeInstance = t.idTypeInstance)
        {$whereType} 
        ORDER BY 2

HERE;
        $query = $this->getDb()->getQueryCommand($cmd);
        return $query;
    }

}
