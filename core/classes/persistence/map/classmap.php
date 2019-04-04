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

class ClassMap
{

    private $namespace;
    private $name;
    private $databaseName;
    private $db;
    private $platform;
    private $tableName;
    private $superClassName;
    private $superClassMap = NULL;
    private $superAssociationMap = NULL;
    private $fieldMaps = array();
    private $attributeMaps = array();
    private $hashedAttributeMaps = array();
    private $keyAttributeMaps = array();
    private $updateAttributeMaps = array();
    private $insertAttributeMaps = array();
    private $referenceAttributeMaps = array();
    private $handledAttributeMaps = array();
    private $associationMaps = array();
    private $selectStatement;
    private $updateStatement;
    private $insertStatement;
    private $deleteStatement;
    private $manager;
    private $hasTypedAttribute = FALSE;

    public function __construct($name, $databaseName, \IPersistentManager $manager = null)
    {
        $this->name = $name;
        $p = strrpos($name, '\\');
        $this->namespace = substr($name, 0, $p);
        $this->databaseName = $databaseName;
        $this->manager = ($manager) ?: PersistentManager::getInstance();
        $this->db = $this->manager->getConnection($databaseName);
        $this->platform = $this->db->getPlatform();
        $this->hasTypedAttribute = FALSE;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setNamespace($value)
    {
        $this->namespace = $value;
    }

    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    public function setDatabaseName($databaseName)
    {
        $this->databaseName = $databaseName;
    }

    public function getDb()
    {
        return $this->db;
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function getTableName($alias = '')
    {
        return $this->tableName . ($alias ? ' ' . $alias : '');
    }

    /*
      public function getTables() {
      return $this->tables;
      }

      public function getTable() {
      reset($this->tables);
      return pos($this->tables);
      }

      public function addTable($tableName) {
      $this->tables[$tableName] = $tableName;
      }
     */

    public function setHasTypedAttribute($has)
    {
        $this->hasTypedAttribute = $has;
    }

    public function getHasTypedAttribute()
    {
        return $this->hasTypedAttribute;
    }

    /**
     * @return PersistentObject
     */
    public function getObject()
    {
        $className = $this->getName();
        $object = new $className;
        return $object;
    }

    public function setSuperClassName($superClassName)
    {
        if (($superClassName != 'businessmodel') && ($superClassName != 'persistentobject')) {
            $this->superClassName = $superClassName;
            $this->superClassMap = $this->manager->getClassMap($superClassName);
        }
    }

    public function getSuperClassMap()
    {
        return $this->superClassMap;
    }

    public function setSuperAssociationMap($associationMap)
    {
        $this->superAssociationMap = $associationMap;
    }

    public function getSuperAssociationMap()
    {
        return $this->superAssociationMap;
    }

    public function addAttributeMap($attributeMap)
    {
        $this->hashedAttributeMaps[$attributeMap->getName()] = $attributeMap;
        $columnName = $attributeMap->getColumnName();
        if ($columnName != '') {
            $this->attributeMaps[] = $attributeMap;

            $this->fieldMaps[strtoupper($columnName)] = $attributeMap;

            if ($attributeMap->getKeyType() == 'primary') {
                $this->keyAttributeMaps[] = $attributeMap;
            } else {
                $this->updateAttributeMaps[] = $attributeMap;
            }

            if ($attributeMap->getIdGenerator() != 'identity') {
                $this->insertAttributeMaps[] = $attributeMap;
            }

            if ($attributeMap->getReference() != NULL) {
                $this->referenceAttributeMaps[] = $attributeMap;
            }
            if ($attributeMap->getHandled()) {
                $this->handledAttributeMaps[] = $attributeMap;
            }
        }
    }

    public function getAttributeMap($name, $areSuperClassesIncluded = false)
    {
        $attributeMap = null;
        $classMap = $this;

        if (is_string($name)) {
            do {
                $attributeMap = $classMap->hashedAttributeMaps[$name];
                $classMap = $classMap->superClassMap;
            } while ($areSuperClassesIncluded && ($attributeMap == null) && ($classMap != null));
        } else {
            $attributeMap = $classMap->attributeMaps[$name];
        }
        return $attributeMap;
    }

    public function getKeyAttributeMap($index = 0)
    {
        return $this->keyAttributeMaps[$index];
    }

    public function getUpdateAttributeMap($index = 0)
    {
        return $this->updateAttributeMaps[$index];
    }

    public function getInsertAttributeMap($index = 0)
    {
        return $this->insertAttributeMaps[$index];
    }

    public function getReferenceAttributeMap($index = 0)
    {
        return $this->referenceAttributeMaps[$index];
    }

    public function getAssociationMap($name)
    {
        $associationMap = NULL;
        $classMap = $this;
        do {
            $associationMap = $classMap->associationMaps[$name];
            if ($associationMap != NULL) {
                $associationMap->setKeysAttributes();
            }
            $classMap = $classMap->superClassMap;
        } while (($associationMap == NULL) && ($classMap != NULL));
        return $associationMap;
    }

    public function putAssociationMap($associationMap)
    {
        $this->associationMaps[$associationMap->getName()] = $associationMap;
    }

    public function getAssociationMaps()
    {
        return $this->associationMaps;
    }

    public function getSize()
    {
        return count($this->attributeMaps);
    }

    public function getReferenceSize()
    {
        return count($this->referenceAttributeMaps);
    }

    public function getAssociationSize()
    {
        return count($this->associationMaps);
    }

    public function getKeyAttributeName($index = 0)
    {
        return $this->keyAttributeMaps[$index]->getName();
    }

    public function getKeySize()
    {
        return count($this->keyAttributeMaps);
    }

    public function getUpdateSize()
    {
        return count($this->updateAttributeMaps);
    }

    public function getInsertSize()
    {
        return count($this->insertAttributeMaps);
    }

    /**
     * Se existir um campo do tipo UID no map ele é setado automaticamente aqui.
     * @param PersistentObject $object
     */
    public function setObjectUid(\PersistentObject $object)
    {
        $field = $this->getUidField();
        if ($field) {
            $setter = 'set' . ucfirst($field);
            $object->$setter(MUtil::generateUID());
        }
    }

    public function setObjectKey($object)
    {
        for ($i = 0; $i < $this->getKeySize(); $i++) {
            $keyAttributeMap = $this->getKeyAttributeMap($i);
            if ($keyAttributeMap->getKeyType() == 'primary') {
                $idGenerator = $keyAttributeMap->getIdGenerator();
                if ($idGenerator != NULL) {
                    if ($idGenerator != 'identity') {
                        $value = $object->getNewId($keyAttributeMap->getIdGenerator());
                    }
                } else {
                    $value = $object->getAttributeValue($keyAttributeMap);
                }
                $object->setAttributeValue($keyAttributeMap, $value);
            }
        }
    }

    public function setPostObjectKey($object)
    {
        $keyAttributeMap = $this->getKeyAttributeMap(0);
        $idGenerator = $keyAttributeMap->getIdGenerator();
        if ($idGenerator == 'identity') {
            $value = $this->db->lastInsertId();
            $object->setAttributeValue($keyAttributeMap, $value);
        }
    }

    public function setObject($object, $data, $classMap = NULL)
    {
        if (is_null($classMap)) {
            $classMap = $this;
        }
        foreach ($data as $field => $value) {
            if (($attributeMap = $classMap->fieldMaps[strtoupper($field)]) || ($attributeMap = $classMap->superClassMap->fieldMaps[strtoupper($field)])) {
                $object->setAttributeValue($attributeMap, $attributeMap->getValueFromDb($value));
            }
        }
    }

    public function retrieveObjectFromData($object, $data)
    {
        $classMap = $this;
        if ($data) {
            do {
                $this->setObject($object, $data, $classMap);
                $classMap = $classMap->superClassMap;
            } while ($classMap != NULL);
            $object->setPersistent(TRUE);
        }
    }

    public function retrieveObject($object, $query)
    {
        $data = $query->fetchObject();
        $this->retrieveObjectFromData($object, $data);
    }

    public function retrieveAssociation(Association $association, $query)
    {
        $query->fetchAll();
        $association->init($query);
    }

    public function getSelectSqlFor($object)
    {
        $statement = $this->getSelectStatement();
        $func = function ($attributeMap) use ($object, $statement) {
            $value = $attributeMap->getValueToDb($object);
            $statement->addParameter($value);
        };
        array_walk($this->keyAttributeMaps, $func);
        return $statement;
    }

    public function getSelectSql($alias = '')
    {
        $classMap = $this;
        do {
            foreach ($classMap->attributeMaps as $attributeMap) {
                $columns[] = $attributeMap->getColumnNameToDb($alias, TRUE);
            }
            $classMap = $classMap->superClassMap;
        } while ($classMap != NULL);
        return implode(',', $columns);
    }

    public function getFromSql()
    {
        $classMap = $this;
        do {
            $tables[] = $classMap->tableName;
            $classMap = $classMap->superClassMap;
        } while ($classMap != NULL);
        return implode(',', $tables);
    }

    public function getWhereSql()
    {
        $inheritanceAssociations = $this->getInheritanceAssociations();
        if (($this->getKeySize() > 0) || ($inheritanceAssociations != '')) {
            foreach ($this->keyAttributeMaps as $attributeMap) {
                $column = $attributeMap->getFullyQualifiedName(null);
                $conditions[] = "(" . $column . " = ?)";
            }
            if ($inheritanceAssociations != '') {
                $conditions[] = $inheritanceAssociations;
            }
        }
        return implode(' AND ', $conditions);
    }

    public function getInheritanceAssociations()
    {
        $classMap = $this;
        $conditions = array();
        do {
            for ($i = 0; $i < $classMap->getReferenceSize(); $i++) {
                $attributeMap = $classMap->getReferenceAttributeMap($i);
                $columnLeft = $attributeMap->getFullyQualifiedName();
                $columnRight = $attributeMap->getReference()->getFullyQualifiedName();
                $conditions[] = "(" . $columnLeft . " = " . $columnRight . ")";
            }
            $classMap = $classMap->superClassMap;
        } while ($classMap != NULL);
        return implode(' AND ', $conditions);
    }

    public function getUpdateSqlFor($object)
    {
        $statement = $this->getUpdateStatement();

        $funcUpdate = function ($attributeMap) use ($object, $statement) {
            $value = $attributeMap->getValueToDb($object);
            $statement->addParameter($value);
        };
        array_walk($this->updateAttributeMaps, $funcUpdate);

        $funcKey = function ($attributeMap) use ($object, $statement) {
            $value = $attributeMap->getValueToDb($object);
            $statement->addParameter($value);
        };
        array_walk($this->keyAttributeMaps, $funcKey);
        return $statement;
    }

    public function getUpdateSql()
    {
        return $this->getTableName();
    }

    public function getUpdateSetSql()
    {
        $classMap = $this;
        do {
            foreach ($this->updateAttributeMaps as $attributeMap) {
                $columns[] = $attributeMap->getColumnName($alias, TRUE);
            }
            $classMap = $classMap->superClassMap;
        } while ($classMap != NULL);
        return implode(',', $columns);
    }

    public function getUpdateWhereSql()
    {
        $classMap = $this;
        foreach ($this->keyAttributeMaps as $attributeMap) {
            $column = $attributeMap->getFullyQualifiedName($alias);
            $conditions[] = "(" . $column . " = ?)";
        }
        if ($inheritanceAssociations != '') {
            $conditions[] = $inheritanceAssociations;
        }
        return implode(' AND ', $conditions);
    }

    public function getInsertSqlFor($object)
    {
        $statement = $this->getInsertStatement();

        $funcInsert = function ($attributeMap) use ($object, $statement) {
            $value = $attributeMap->getValueToDb($object);
            $statement->addParameter($value);
        };
        array_walk($this->insertAttributeMaps, $funcInsert);
        return $statement;
    }

    public function getInsertSql()
    {
        return $this->getTableName();
    }

    public function getInsertValuesSql()
    {
        $classMap = $this;
        do {
            foreach ($this->insertAttributeMaps as $attributeMap) {
                $columns[] = $attributeMap->getColumnName();
            }
            $classMap = $classMap->superClassMap;
        } while ($classMap != NULL);
        return implode(',', $columns);
    }

    public function getDeleteSqlFor($object)
    {
        $statement = $this->getDeleteStatement();

        $funcKey = function ($attributeMap) use ($object, $statement) {
            $value = $attributeMap->getValueToDb($object);
            $statement->addParameter($value);
        };
        array_walk($this->keyAttributeMaps, $funcKey);
        return $statement;
    }

    public function getDeleteSql()
    {
        return $this->getTableName();
    }

    public function getDeleteWhereSql()
    {
        $classMap = $this;
        foreach ($this->keyAttributeMaps as $attributeMap) {
            $column = $attributeMap->getFullyQualifiedName($alias);
            $conditions[] = "(" . $column . " = ?)";
        }
        if ($inheritanceAssociations != '') {
            $conditions[] = $inheritanceAssociations;
        }
        return implode(' AND ', $conditions);
    }

    public function getSelectStatement()
    {
        $this->selectStatement = new \database\MSQL();
        $this->selectStatement->setDb($this->getDb());
        $this->selectStatement->setColumns($this->getSelectSql());
        $this->selectStatement->setTables($this->getFromSql());
        $this->selectStatement->setWhere($this->getWhereSql());
        return $this->selectStatement;
    }

    public function getUpdateStatement()
    {
        $this->updateStatement = new \database\MSQL();
        $this->updateStatement->setDb($this->getDb());
        $this->updateStatement->setColumns($this->getUpdateSetSql());
        $this->updateStatement->setTables($this->getUpdateSql());
        $this->updateStatement->setWhere($this->getUpdateWhereSql());
        return $this->updateStatement;
    }

    public function getInsertStatement()
    {
        $this->insertStatement = new \database\MSQL();
        $this->insertStatement->setDb($this->getDb());
        $this->insertStatement->setColumns($this->getInsertValuesSql());
        $this->insertStatement->setTables($this->getInsertSql());
        return $this->insertStatement;
    }

    public function getDeleteStatement()
    {
        $this->deleteStatement = new \database\MSQL();
        $this->deleteStatement->setDb($this->getDb());
        $this->deleteStatement->setTables($this->getDeleteSql());
        $this->deleteStatement->setWhere($this->getDeleteWhereSql());
        return $this->deleteStatement;
    }

    public function handleTypedAttribute($object, $operation)
    {
        foreach ($this->handledAttributeMaps as $attributeMap) {
            $cmd[] = array($this->getPlatform(), $attributeMap, $operation, $object);
        }
        return $cmd;
    }

    public function getUidField()
    {
        foreach ($this->attributeMaps as $attributeMap) {
            if ($attributeMap->getIdGenerator() === 'uid') {
                return $attributeMap->getName();
            }
        }
        return null;
    }

}
