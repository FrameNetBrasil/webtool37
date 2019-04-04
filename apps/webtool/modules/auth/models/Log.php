<?php

namespace fnbr\auth\models;

class Log extends map\LogMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'ts' => array('notnull'),
                'operation' => array('notnull'),
                'idUser' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdLog();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idLog');
        if ($filter->idLog){
            $criteria->where("idLog LIKE '{$filter->idLog}%'");
        }
        return $criteria;
    }
}
