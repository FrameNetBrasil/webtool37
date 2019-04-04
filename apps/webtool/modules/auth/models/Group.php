<?php

namespace fnbr\auth\models;

class Group extends map\GroupMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'name' => array('notnull'),
                'description' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getName();
    }

    public function getByName($name){
        $criteria = $this->getCriteria()->select('*');
        $criteria->where("upper(name) = upper('{$name}')");
        $this->retrieveFromCriteria($criteria);
    }

    public function listByFilter($filter = ''){
        $criteria = $this->getCriteria()->select('*')->orderBy('idGroup');
        if ($filter->idGroup){
            $criteria->where("idGroup LIKE '{$filter->idGroup}%'");
        }
        return $criteria;
    }

    public function listUser() {
        $criteria = $this->getCriteria()->select('users.idUser, users.login')->orderBy('users.login');
        if ($this->idGroup) {
            $criteria->where("idGroup = {$this->idGroup}");
        }
        return $criteria;
    }
}

