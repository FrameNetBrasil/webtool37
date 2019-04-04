<?php

/**
 * DataType dos valores de sexo
 */
class MSexo extends MEnumBase
{

    const MASCULINO = "M";
    const FEMININO = "F";
    const INDEFINIDO = "I";

    public function getDefault()
    {
        return '';
    }

}


