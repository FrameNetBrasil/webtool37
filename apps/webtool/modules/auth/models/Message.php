<?php

namespace fnbr\auth\models;

class Message extends map\MessageMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'idUser' => array('notnull'),
                'idMsgStatus' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdMessage();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idMessage');
        if ($filter->idMessage){
            $criteria->where("idMessage LIKE '{$filter->idMessage}%'");
        }
        return $criteria;
    }
}
