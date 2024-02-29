<?php

namespace fnbr\models;

class StatusMM extends map\StatusMMMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(),
            'converters' => array()
        );
    }

}
