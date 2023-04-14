<?php

namespace fnbr\models;

class OriginMM extends map\OriginMMMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(),
            'converters' => array()
        );
    }

    public function getLookup() {
        $criteria = $this->getCriteria()
            ->select('idOriginMM,origin')
            ->asQuery();
        return $criteria->getResult();
    }

}
