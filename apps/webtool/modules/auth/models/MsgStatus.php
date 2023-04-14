<?php

namespace fnbr\auth\models;

class MsgStatus extends map\MsgStatusMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdMsgStatus();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idMsgStatus');
        if ($filter->idMsgStatus){
            $criteria->where("idMsgStatus LIKE '{$filter->idMsgStatus}%'");
        }
        return $criteria;
    }
}

