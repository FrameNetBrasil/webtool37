<?php

namespace fnbr\auth\models;

class MessageBox extends map\MessageBoxMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'idUser' => array('notnull'),
                'idMsgStatus' => array('notnull'),
                'idMessage' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getIdMessageBox();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idMessageBox');
        if ($filter->idMessageBox){
            $criteria->where("idMessageBox LIKE '{$filter->idMessageBox}%'");
        }
        return $criteria;
    }
}
