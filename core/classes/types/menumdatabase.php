<?php
/* Copyright [2011, 2013, 2017] da Universidade Federal de Juiz de Fora
 * Este arquivo é parte do programa Framework Maestro.
 * O Framework Maestro é um software livre; você pode redistribuí-lo e/ou
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada
 * pela Fundação do Software Livre (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil,
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL
 * em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título
 * "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software
 * Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a
 * Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

/**
 * Classe base para Enumerações armazenadas no Banco de Dados (ex. "Tabela Geral")
 */
class MEnumDatabase extends MEnumBase
{

    //protected static $model = '';
    //protected static $table = '';

    public static function __callStatic($name, $arguments = NULL)
    {
        return self::getByConstant($name);
    }

    public function getDefault()
    {
        return '';
    }

    /**
     * Retorna a lista de constantes da classe filha à classe EnumBase
     * @return type
     */
    public static function listAll()
    {
        $model = static::$model;
        $oClass = new $model;
        $filter = new StdClass();
        $filter->table = static::$table;
        $constants = $oClass->listByFilter($filter)->asQuery()->chunkResult(2, 3);
        return $constants;
    }

    /**
     * Retorna uma lista de constantes específica da classe filha à classe EnumBase
     * @return type
     */
    public static function listByValues($arrayValues)
    {
        $allValues = self::listAll();

        if (is_array($arrayValues)) {
            foreach ($arrayValues as $value) {
                if (isset($allValues[$value])) {
                    $result[$value] = $allValues[$value];
                }
            }
        }

        return $result;
    }

    public static function getById($id)
    {
        $model = static::$model;
        $oClass = new $model;
        $filter = new StdClass();
        $filter->table = static::$table;
        $filter->item1 = $id;
        $constants = $oClass->listByFilter($filter)->asQuery()->chunkResult(2, 3);
        return $constants[$id];
    }

    public static function getByConstant($constant)
    {
        $model = static::$model;
        $oClass = new $model;
        $filter = new StdClass();
        $filter->table = static::$table;
        $filter->item2 = $constant;
        $constants = $oClass->listByFilter($filter)->asQuery()->chunkResult(3, 2);
        return $constants[$constant];
    }

    /**
     * Indica se o parâmetro é um valor válido para a Enum
     */
    public static function isValid($value)
    {
        $constant = self::getById($value);
        return ($constant != '');
    }

}