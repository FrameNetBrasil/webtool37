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

class AttributeMap
{

    private $classMap;
    private $name;
    private $columnName;
    private $alias;
    private $reference;
    private $index = NULL;
    private $type = NULL;
    private $converter = NULL;
    private $handled = false;
    private $keyType;
    private $idGenerator;
    private $db;
    private $platform;

    public function __construct($name, $classMap)
    {
        $this->name = $name;
        $this->classMap = $classMap;
        $this->db = $classMap->getDb();
        $this->platform = $classMap->getPlatform();
    }

    public function getClassMap()
    {
        return $this->classMap;
    }

    public function getName($alias = '')
    {
        if ($alias != '') {
            return $alias . '.' . $this->name;
        } else {
            return $this->name;
        }
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getHandled()
    {
        return $this->handled;
    }

    public function setHandled($handled)
    {
        $this->handled = $handled;
    }

    public function setColumnName($name)
    {
        $this->columnName = $name;
    }

    public function setIndex($index)
    {
        $this->index = $index;
    }

    public function setConverter($converter)
    {
        $this->converter = $converter;
    }

    public function getConverter()
    {
        return $this->converter;
    }

    public function setValue($object, $value)
    {
        if (($pos = strpos($this->name, '.')) !== FALSE) {
            $nested = substr($this->name, 0, $pos);
            $nestedObject = $object->get($nested);
            if (is_null($nestedObject)) {
                $classMap = $object->getClassMap();
                $associationMap = $classMap->getAssociationMap($nested);
                $toClassMap = $associationMap->getToClassMap();
                $nestedObject = $toClassMap->getObject();
                $object->set(substr($this->name, $pos + 1), $nestedObject);
            }
            $nestedObject->$setAttribute($value);
        } elseif ($this->index) {
            $object->set($this->name . $this->index, $value);
        } else {
            $object->set($this->name, $value);
        }
    }

    public function getValue($object)
    {
        return $object->get($this->index ? $this->name . $this->index : $this->name);
    }

    public function setReference($attributeMap)
    {
        $this->reference = $attributeMap;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function getTableName()
    {
        return $this->classMap->getTableName();
    }

    public function setKeyType($type)
    {
        $this->keyType = $type;
    }

    public function getKeyType()
    {
        return $this->keyType;
    }

    public function setIdGenerator($idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function getIdGenerator()
    {
        return $this->idGenerator;
    }

    public function getColumnName()
    {
        return $this->columnName;
    }

    public function getFullyQualifiedName($alias = '')
    {
        if ($alias != '') {
            $name = $alias . '.' . $this->columnName;
        } else {
            $name = $this->getTableName() . '.' . $this->columnName;
        }
        return $name;
    }

    public function convertValue($value)
    {
        if (is_array($this->converter)) {
            foreach ($this->converter as $conv => $args) {
                $charset = \Manager::getConf("options.charset");
                if ($conv == 'case') {
                    if ($args == 'upper') {
                        $value = mb_strtoupper($value, $charset);
                    } elseif ($args == 'lower') {
                        $value = mb_strtolower($value, $charset);
                    } elseif ($args == 'ucwords') {
                        $value = ucwords($value);
                    }
                } else if ($conv == 'trim') {
                    if ($args == 'left') {
                        $value = ltrim($value);
                    } elseif ($args == 'right') {
                        $value = rtrim($value);
                    } elseif ($args == 'all') {
                        $value = trim($value);
                    }
                } else if ($conv == 'default') {
                    if ($value instanceOf MType) {
                        $rawValue = $value->getValue();
                        if ($rawValue == '') {
                            $value->setValue($args);
                        }
                    } else {
                        $value = ($value ?: $args);
                    }
                }
            }
        }
        return $value;
    }

    public function getValueToDb($object)
    {
        $value = $this->convertValue($this->getValue($object));
        if (is_string($value)) {
            $value = $object->sanitize($this->name, $value);
        }

        return array($value, $this->type);
    }

    public function getValueFromDb($value)
    {
        return $this->platform->convertToPHPValue($value, $this->type);
    }

    public function getColumnNameToDb($criteriaAlias = '', $as = TRUE)
    {
        $fullyName = $this->getFullyQualifiedName($criteriaAlias);
        $name = $this->platform->convertColumn($fullyName, $this->type);
        if ($as && ($name != $fullyName)) { // need a "as" clause
            $name .= ' AS ' . $this->name;
        }
        return $name;
    }

    public function getColumnWhereName($criteriaAlias = '')
    {
        $fullyName = $this->getFullyQualifiedName($criteriaAlias);
        $name = $this->platform->convertWhere($fullyName, $this->type);
        return $name;
    }
}
