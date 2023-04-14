<?php

namespace fnbr\models;

class WordMM extends map\WordMMMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
            ),
            'converters' => array()
        );
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idWordMM');
        if ($filter->idDocumentMM){
            $criteria->where("idDocumentMM = {$filter->idDocumentMM}");
        }
        if ($filter->origin){
            $criteria->where("origin = {$filter->origin}");
        }
        return $criteria;
    }


}