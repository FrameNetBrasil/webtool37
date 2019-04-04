<?php

namespace fnbr\auth\models;

class Person extends map\PersonMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
                'name' => array('notnull'),
                'email' => array('notnull'),
                'nick' => array('notnull'),
            ),
            'converters' => array()
        );
    }
    
    public function getDescription(){
        return $this->getName();
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idPerson');
        if ($filter->idPerson){
            $criteria->where("idPerson = {$filter->idPerson}");
        }
        if ($filter->auth0IdUser){
            $criteria->where("auth0IdUser = '{$filter->auth0IdUser}'");
        }
        return $criteria;
    }

    public function listForLookup(){
        $criteria = $this->getCriteria()->select('idPerson, name')->orderBy('name');
        return $criteria;
    }
    
}
