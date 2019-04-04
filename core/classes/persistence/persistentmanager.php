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

class PersistentManager implements \IPersistentManager
{

    static private $instance = NULL;
    static private $container = NULL;
    private $dbConnections = array();
    private $classMaps = array();
    private $converters = array();
    private $debug = false;
    private $locked = false;
    private $configLoader;

    public static function getInstance($configLoader = 'PHP')
    {
        if (self::$instance == NULL) {
            $manager = self::$instance = new PersistentManager();
            self::$container = Manager::getInstance();
            $manager->setConfigLoader($configLoader);
        }
        return self::$instance;
    }

    public function setConfigLoader($configLoader = 'PHP')
    {
        $this->configLoader = ($configLoader == 'PHP') ? new PHPConfigLoader($this) : new XMLConfigLoader($this);
    }

    public function getConfigLoader()
    {
        return $this->configLoader;
    }

    public function addClassMap($name, $classMap)
    {
        $this->classMaps[$name] = $classMap;
    }

    /**
     * @param $className
     * @param string $mapClassName
     * @return ClassMap
     */
    public function getClassMap($className, $mapClassName = '')
    {
        $classMap = $this->classMaps[$className];
        if (!$classMap) {
            $classMap = $this->configLoader->getClassMap($className, $mapClassName);
            $this->addClassMap($className, $classMap);
        }
        return $classMap;
    }

    public function getConverter($name)
    {
        return $this->converters[$name];
    }

    public function putConverter($name, $converter)
    {
        $this->converters[$name] = $converter;
    }

    private function logger(&$commands, ClassMap $classMap, PersistentObject $object, $operation)
    {
        $logger = $classMap->getDb()->getORMLogger();
        if ($object->logIsEnabled() && $logger) {
            $description = $object->getLogDescription();
            $idMethod = 'get' . $classMap->getKeyAttributeName();
            $commands[] = $logger->getCommand($operation, $classMap->getName(), $object->$idMethod(), $description);
        }
    }

    private function execute(database\MDatabase $db, $commands)
    {
        if (!is_array($commands)) {
            $commands = array($commands);
        }
        $db->executeBatch($commands);
    }

    /**
     * Retrieve Object
     *
     */
    public function retrieveObject(PersistentObject $object)
    {
        $classMap = $object->getClassMap();
        $this->_retrieveObject($object, $classMap);
    }

    private function _retrieveObject(PersistentObject $object, ClassMap $classMap)
    {
        $statement = $classMap->getSelectSqlFor($object);
        $query = $classMap->getDb()->getQuery($statement);
        $this->retrieveObjectFromCacheOrQuery($object, $classMap, $query);
        $this->_retrieveAssociations($object, $classMap);
    }

    public function retrieveObjectFromCriteria(PersistentObject $object, PersistentCriteria $criteria, $parameters = NULL)
    {
        $classMap = $object->getClassMap();
        $query = $this->processCriteriaQuery($criteria, $parameters, $classMap->getDb(), FALSE);
        $this->retrieveObjectFromQuery($object, $query);
    }

    public function retrieveObjectFromQuery(PersistentObject $object, Database\MQuery $query)
    {
        $classMap = $object->getClassMap();
        $classMap->retrieveObject($object, $query);
        $this->_retrieveAssociations($object, $classMap);
    }

    /**
     * Retrieve Associations
     *
     */
    public function retrieveAssociations(PersistentObject $object)
    {
        $classMap = $object->getClassMap();
        $this->_retrieveAssociations($object, $classMap);
    }

    public function _retrieveAssociations(PersistentObject $object, ClassMap $classMap)
    {
        if ($classMap->getSuperClassMap() != NULL) {
            $this->_retrieveAssociations($object, $classMap->getSuperClassMap());
        }
        $associationMaps = $classMap->getAssociationMaps();
        foreach ($associationMaps as $associationMap) {
            if ($associationMap->isRetrieveAutomatic()) {
                $associationMap->setKeysAttributes();
                $this->__retrieveAssociation($object, $associationMap, $classMap);
            }
        }
    }

    public function retrieveAssociation(PersistentObject $object, $associationName)
    {
        $classMap = $object->getClassMap();
        $this->_retrieveAssociation($object, $associationName, $classMap);
    }

    private function _retrieveAssociation(PersistentObject $object, $associationName, ClassMap $classMap)
    {
        $associationMap = $classMap->getAssociationMap($associationName);
        if (is_null($associationMap)) {
            throw new EPersistentException("Association name [{$associationName}] not found.");
        }
        $this->__retrieveAssociation($object, $associationMap, $classMap);
    }

    private function __retrieveAssociation(PersistentObject $object, AssociationMap $associationMap, ClassMap $classMap)
    {
        $orderAttributes = $associationMap->getOrderAttributes();
        $criteria = $associationMap->getCriteria($orderAttributes);
        $criteriaParameters = $associationMap->getCriteriaParameters($object);
        $query = $this->processCriteriaQuery($criteria, $criteriaParameters, $classMap->getDb(), FALSE);
        mtrace('=== retrieving Associations for class ' . $classMap->getName());
        $toClassMap = $associationMap->getToClassMap();
        if ($associationMap->getCardinality() == 'oneToOne') {
            $association = $this->loadSingleAssociation($toClassMap, $criteriaParameters[0], $query);
        } elseif (($associationMap->getCardinality() == 'oneToMany') || ($associationMap->getCardinality() == 'manyToMany')) {
            // association is an Association object
            $index = $associationMap->getIndexAttribute();
            $association = new Association($toClassMap, $index);
            $toClassMap->retrieveAssociation($association, $query);
        }
        $object->set($associationMap->getName(), $association);
    }

    private function loadSingleAssociation(ClassMap $classMap, $id, \database\MQuery $query)
    {
        $association = $classMap->getObject();
        $association->set($association->getPKName(), $id);
        $this->retrieveObjectFromCacheOrQuery($association, $classMap, $query);
        return $association;
    }

    private function retrieveObjectFromCacheOrQuery(PersistentObject $object, ClassMap $classMap, \database\MQuery $query)
    {
        $cacheManager = CacheManager::getInstance();
        $useCache = $cacheManager->isCacheable($object) && $cacheManager->cacheIsEnabled();
        $cacheMiss = true;

        if ($useCache) {
            $cacheMiss = !$cacheManager->load($object, $object->getId());
        }

        if ($cacheMiss) {
            $classMap->retrieveObject($object, $query);
        }

        if ($useCache && $cacheMiss && $object->isPersistent()) {
            $cacheManager->save($object);
        }
    }

    public function retrieveAssociationAsCursor(PersistentObject $object, $target)
    {
        $classMap = $object->getClassMap();
        $this->_retrieveAssociationAsCursor($object, $target, $classMap);
    }

    private function _retrieveAssociationAsCursor(PersistentObject $object, $associationName, ClassMap $classMap)
    {
        $associationMap = $classMap->getAssociationMap($associationName);
        if (is_null($associationMap)) {
            throw new EPersistentException("Association name [{$associationName}] not found.");
        }
        $orderAttributes = $associationMap->getOrderAttributes();
        $criteria = $associationMap->getCriteria($orderAttributes);
        $criteriaParameters = $associationMap->getCriteriaParameters($object);
        $cursor = $this->processCriteriaCursor($criteria, $criteriaParameters, $classMap->getDb(), FALSE);
        $object->set($associationMap->getName(), $cursor);
    }

    public function saveObject(PersistentObject $object)
    {
        $object->validate();
        $classMap = $object->getClassMap();
        $commands = array();
        $this->_saveObject($object, $classMap, $commands);
        $this->execute($classMap->getDb(), $commands);
        if (!$object->getId()) {
            $classMap->setPostObjectKey($object);
        }

        $this->deleteFromCache($object);
    }

    private function _saveObject(PersistentObject $object, ClassMap $classMap, &$commands)
    {
        if ($classMap->getSuperClassMap() != NULL) {
            $isPersistent = $object->isPersistent();
            $this->_saveObject($object, $classMap->getSuperClassMap(), $commands);
            $object->setPersistent($isPersistent);
        }

        $operation = $object->isPersistent() ? 'update' : 'insert';
        if ($operation == 'update') {
            $statement = $classMap->getUpdateSqlFor($object);
            $commands[] = $statement->update();
        } else {
            $classMap->setObjectKey($object);
            $classMap->setObjectUid($object);
            $statement = $classMap->getInsertSqlFor($object);
            $commands[] = $statement->insert();
        }
        if ($cmd = $classMap->handleTypedAttribute($object, $operation)) {
            $commands[] = $cmd;
        }
        $this->logger($commands, $classMap, $object, $operation);

        $mmCmd = array();

        $associationMaps = $classMap->getAssociationMaps();
        foreach ($associationMaps as $associationMap) {
            if ($associationMap->isSaveAutomatic()) {
                $this->__saveAssociation($object, $associationMap, $mmCmd, $classMap);
            }
        }

        if (count($mmCmd)) {
            $commands = array_merge($commands, $mmCmd);
        }
        $object->setPersistent(true);
    }

    public function saveObjectRaw(PersistentObject $object)
    {
        $object->validate();
        $classMap = $object->getClassMap();
        $commands = array();
        $this->_saveObjectRaw($object, $classMap, $commands);
        $this->execute($classMap->getDb(), $commands);
    }

    private function _saveObjectRaw(PersistentObject $object, ClassMap $classMap, &$commands)
    {
        if ($object->isPersistent()) {
            $statement = $classMap->getUpdateSqlFor($object);
            $commands[] = $statement->update();
        } else {
            $classMap->setObjectKey($object);
            $statement = $classMap->getInsertSqlFor($object);
            $commands[] = $statement->insert();
        }
        $object->setPersistent(true);
    }

    /**
     * Save Associations
     *
     */
    public function saveAssociation(PersistentObject $object, $associationName)
    {
        $classMap = $object->getClassMap();
        $commands = array();
        $this->_saveAssociation($object, $associationName, $commands, $classMap);
        $this->execute($classMap->getDb(), $commands);
    }

    private function _saveAssociation(PersistentObject $object, $associationName, &$commands, ClassMap $classMap)
    {
        $associationMap = $classMap->getAssociationMap($associationName);
        if (is_null($associationMap)) {
            throw new EPersistentException("Association name [{$associationName}] not found.");
        }
        $this->__saveAssociation($object, $associationMap, $commands, $classMap, $id);
    }

    private function __saveAssociation(PersistentObject $object, AssociationMap $associationMap, &$commands, ClassMap $classMap)
    {
        $toAttributeMap = $associationMap->getToAttributeMap();
        $fromAttributeMap = $associationMap->getFromAttributeMap();
        if ($associationMap->getCardinality() == 'oneToOne') {
            // obtem o objeto referenciado
            $refObject = $object->get($associationMap->getName());
            if ($refObject != NULL) {
                // se a associação é inversa, atualiza o objeto referenciado
                if ($associationMap->isInverse()) {
                    $refObject->setAttributeValue($toAttributeMap, $object->getAttributeValue($fromAttributeMap));
                    $this->_saveObject($refObject, $associationMap->getToClassMap(), $commands);
                } else { // se a associação é direta, atualiza o próprio objeto
                    $object->setAttributeValue($fromAttributeMap, $refObject->getAttributeValue($toAttributeMap));
                    $this->_saveObject($object, $classMap, $commands);
                }
            }
        } elseif ($associationMap->getCardinality() == 'oneToMany') {
            // atualiza os objetos referenciados
            $collection = $object->get($associationMap->getName());
            if (count($collection) > 0) {
                foreach ($collection as $refObject) {
                    if ($refObject != NULL) {
                        $refObject->setAttributeValue($toAttributeMap, $object->getAttributeValue($fromAttributeMap));
                        $this->_saveObject($refObject, $associationMap->getToClassMap(), $commands);
                    }
                }
            }
        } elseif ($associationMap->getCardinality() == 'manyToMany') {
            // atualiza a tabela associativa (removendo e reinserindo os registros de associação)
            $commands = array();
            $collection = $object->get($associationMap->getName());
            if ($object->getOIDValue()) {
                $commands[] = $associationMap->getDeleteStatement($object);
            }
            if (count($collection) > 0) {
                foreach ($collection as $refObject) {
                    if ($refObject != NULL) {
                        $commands[] = $associationMap->getInsertStatement($object, $refObject);
                    }
                }
            }
        }
    }

    public function saveAssociationById(PersistentObject $object, $associationName, $id)
    {
        $object->retrieveAssociation($associationName);
        $associationIds = MUtil::parseArray($object->{'get' . $associationName}()->getId());
        //$ids = array_unique(array_merge($associationIds, MUtil::parseArray($id)));
        $classMap = $object->getClassMap();
        $commands = array();
        //$this->_saveAssociationById($object, $associationName, $commands, $classMap, $ids);
        $this->_saveAssociationById($object, $associationName, $commands, $classMap, $id);
        $this->execute($classMap->getDb(), $commands);
    }

    private function _saveAssociationById(PersistentObject $object, $associationName, &$commands, ClassMap $classMap, $id)
    {
        $associationMap = $classMap->getAssociationMap($associationName);
        if (is_null($associationMap)) {
            throw new EPersistentException("Association name [{$associationName}] not found.");
        }
        $this->__saveAssociationById($object, $associationMap, $commands, $classMap, $id);
    }

    private function __saveAssociationById(PersistentObject $object, AssociationMap $associationMap, &$commands, ClassMap $classMap, $id)
    {
        $toAttributeMap = $associationMap->getToAttributeMap();
        $fromAttributeMap = $associationMap->getFromAttributeMap();
        $refObject = $associationMap->getToClassMap()->getObject();
        if ($associationMap->getCardinality() == 'oneToOne') {
            // obtem o objeto referenciado
            if ($refObject != NULL) {
                // se a associação é inversa, atualiza o objeto referenciado
                if ($associationMap->isInverse()) {
                    $refObject->getById($id);
                    $refObject->setAttributeValue($toAttributeMap, $object->getAttributeValue($fromAttributeMap));
                    $this->_saveObject($refObject, $associationMap->getToClassMap(), $commands);
                } else { // se a associação é direta, atualiza o próprio objeto
                    $object->setAttributeValue($fromAttributeMap, $id);
                    $this->_saveObject($object, $classMap, $commands);
                }
            }
        } elseif ($associationMap->getCardinality() == 'oneToMany') {
            // atualiza os objetos referenciados
            $commands[] = $associationMap->getUpdateStatementId($object, $id, $object->getAttributeValue($fromAttributeMap));
        } elseif ($associationMap->getCardinality() == 'manyToMany') {
            $commands = array();
            // atualiza a tabela associativa (removendo e reinserindo os registros de associação)
            $aId = $id;
            if (!is_array($id))
                $aId = array($id);

            if ($object->getId()) {
                //$commands[] = $associationMap->getDeleteStatement($object);
                $commands[] = $associationMap->getDeleteStatementId($object, $aId);
            }
            foreach ($aId as $idRef) {
                $commands[] = $associationMap->getInsertStatementId($object, $idRef);
            }
            //$commands[] = $associationMap->getInsertStatementId($object, $id);
        }
    }

    /**
     * Delete Object
     *
     */
    public function deleteObject(PersistentObject $object)
    {
        $classMap = $object->getClassMap();
        $commands = array();
        $this->_deleteObject($object, $classMap, $commands);
        $this->execute($classMap->getDb(), $commands);
        $this->deleteFromCache($object);
    }

    private function _deleteObject(PersistentObject $object, ClassMap $classMap, &$commands)
    {
        $mmCmd = array();
        $associationMaps = $classMap->getAssociationMaps();
        if (count($associationMaps)) {
            foreach ($associationMaps as $associationMap) {
                if (!$associationMap->isDeleteAutomatic()) {
                    continue;
                }
                $this->__deleteAssociation($object, $associationMap, $mmCmd, $classMap);
            }
        }
        $statement = $classMap->getDeleteSqlFor($object);
        $commands[] = $statement->delete();

        if (count($mmCmd)) {
            $commands = array_merge($mmCmd, $commands);
        }
        $this->logger($commands, $classMap, $object, 'delete');
        if ($classMap->getSuperClassMap() != NULL) {
            $this->_deleteObject($object, $classMap->getSuperClassMap(), $commands);
        }
        $object->setPersistent(FALSE);
    }

    private function deleteFromCache(PersistentObject $object)
    {
        CacheManager::getInstance()->delete($object);
    }

    /**
     * Delete Associations
     *
     */
    public function deleteAssociation(PersistentObject $object, $associationName)
    {
        $classMap = $object->getClassMap();
        $commands = array();
        $this->_deleteAssociation($object, $associationName, $commands, $classMap);
        $this->execute($classMap->getDb(), $commands);
    }

    private function _deleteAssociation(PersistentObject $object, $associationName, &$commands, ClassMap $classMap)
    {
        $associationMap = $classMap->getAssociationMap($associationName);
        if (is_null($associationMap)) {
            throw new EPersistentException("Association name [{$associationName}] not found.");
        }
        $this->__deleteAssociation($object, $associationMap, $commands, $classMap);
    }

    private function __deleteAssociation(PersistentObject $object, AssociationMap $associationMap, &$commands, ClassMap $classMap)
    {
        $toAttributeMap = $associationMap->getToAttributeMap();
        $fromAttributeMap = $associationMap->getFromAttributeMap();
        if ($associationMap->getCardinality() == 'oneToOne') {
            // obtem o objeto referenciado
            $refObject = $object->get($associationMap->getName());
            if ($refObject != NULL) {
                // se a associação é inversa, atualiza o objeto referenciado
                if ($associationMap->isInverse()) {
                    $refObject->setAttributeValue($toAttributeMap, NULL);
                    $this->_saveObject($refObject, $associationMap->getToClassMap(), $commands);
                } else { // se a associação é direta, atualiza o próprio objeto
                    $object->setAttributeValue($fromAttributeMap, NULL);
                    $this->_saveObject($object, $classMap, $commands);
                }
            }
        } elseif ($associationMap->getCardinality() == 'oneToMany') {
            // atualiza os objetos referenciados
            $collection = $object->get($associationMap->getName());
            if (count($collection) > 0) {
                foreach ($collection as $refObject) {
                    if ($refObject != NULL) {
                        $refObject->setAttributeValue($toAttributeMap, NULL);
                        $this->_saveObject($refObject, $associationMap->getToClassMap(), $commands);
                    }
                }
            }
        } elseif ($associationMap->getCardinality() == 'manyToMany') {
            // remove os registros de associação
            $commands = array();
            $collection = $object->get($associationMap->getName());
            if ($object->getOIDValue()) {
                $commands[] = $associationMap->getDeleteStatement($object);
            }
        }
        $associationMap->setKeysAttributes();
        $this->__retrieveAssociation($object, $associationMap, $classMap);
    }

    public function deleteAssociationObject(PersistentObject $object, $associationName, PersistentObject $refObject)
    {
        $classMap = $object->getClassMap();
        $commands = array();
        $this->_deleteAssociationObject($object, $associationName, $refObject, $commands, $classMap);
        $this->execute($classMap->getDb(), $commands);
    }

    private function _deleteAssociationObject(PersistentObject $object, $associationName, PersistentObject $refObject, &$commands, ClassMap $classMap)
    {
        $associationMap = $classMap->getAssociationMap($associationName);
        if (is_null($associationMap)) {
            throw new EPersistentException("Association name [{$associationName}] not found.");
        }
        $this->__deleteAssociationObject($object, $associationMap, $refObject, $commands, $classMap);
    }

    private function __deleteAssociationObject(PersistentObject $object, AssociationMap $associationMap, PersistentObject $refObject, &$commands, ClassMap $classMap)
    {
        $toAttributeMap = $associationMap->getToAttributeMap();
        $fromAttributeMap = $associationMap->getFromAttributeMap();
        if (($associationMap->getCardinality() == 'oneToOne') || ($associationMap->getCardinality() == 'oneToMany')) {
            // obtem o objeto referenciado
            if ($refObject != NULL) {
                // se a associação é inversa, atualiza o objeto referenciado
                if ($associationMap->isInverse()) {
                    $refObject->setAttributeValue($toAttributeMap, NULL);
                    $this->_saveObject($refObject, $associationMap->getToClassMap(), $commands);
                } else { // se a associação é direta, atualiza o próprio objeto
                    $object->setAttributeValue($fromAttributeMap, NULL);
                    $this->_saveObject($object, $classMap, $commands);
                }
            }
        } elseif ($associationMap->getCardinality() == 'manyToMany') {
            // remove os registros da associação com $refObject
            $commands = array();
            if ($object->getOIDValue()) {
                $commands[] = $associationMap->getDeleteStatement($object, $refObject);
            }
        }

        $this->__retrieveAssociation($object, $associationMap, $classMap);
    }

    public function deleteAssociationById(PersistentObject $object, $associationName, $id)
    {
        $classMap = $object->getClassMap();
        $commands = array();
        $this->_deleteAssociationById($object, $associationName, $id, $commands, $classMap);
        $this->execute($classMap->getDb(), $commands);
    }

    private function _deleteAssociationById(PersistentObject $object, $associationName, $id, &$commands, ClassMap $classMap)
    {
        $associationMap = $classMap->getAssociationMap($associationName);
        if (is_null($associationMap)) {
            throw new EPersistentException("Association name [{$associationName}] not found.");
        }
        $this->__deleteAssociationById($object, $associationMap, $id, $commands, $classMap);
    }

    private function __deleteAssociationById(PersistentObject $object, AssociationMap $associationMap, $id, &$commands, ClassMap $classMap)
    {
        $toAttributeMap = $associationMap->getToAttributeMap();
        $fromAttributeMap = $associationMap->getFromAttributeMap();
        if (!is_array($id)) {
            $id = array($id);
        }
        if ($associationMap->getCardinality() == 'oneToOne') {
            if ($associationMap->isInverse()) {
                // obtem o objeto referenciado
                $refObject = $object->get($associationMap->getName());
                $refObject->setAttributeValue($toAttributeMap, NULL);
                $this->_saveObject($refObject, $associationMap->getToClassMap(), $commands);
            } else { // se a associação é direta, atualiza o próprio objeto
                $object->setAttributeValue($fromAttributeMap, NULL);
                $this->_saveObject($object, $classMap, $commands);
            }
        } elseif ($associationMap->getCardinality() == 'oneToMany') {
            $refObject = $associationMap->getToClassMap()->getObject();
            $commands[] = $associationMap->getUpdateStatementId($object, $id, NULL);
        } elseif ($associationMap->getCardinality() == 'manyToMany') {
            $commands[] = $associationMap->getDeleteStatementId($object, $id);
        }
        $associationMap->setKeysAttributes();
        $this->__retrieveAssociation($object, $associationMap, $classMap);
    }

    /**
     * Process Criteria
     *
     */
    private function processCriteriaQuery(PersistentCriteria $criteria, $parameters, database\MDatabase $db, $forProxy = FALSE)
    {
        $statement = $criteria->getSqlStatement($forProxy);
        $statement->setDb($db);
        $statement->setParameters($parameters);
        $query = $db->getQuery($statement);
        return $query;
    }

    private function processCriteriaCursor(PersistentCriteria $criteria, $parameters, database\MDatabase $db, $forProxy = FALSE)
    {
        $query = $this->processCriteriaQuery($criteria, $parameters, $db, $forProxy);
        $cursor = new Cursor($query, $criteria->getClassMap(), $forProxy, $this);
        return $cursor;
    }

    public function processCriteriaDelete(DeleteCriteria $criteria, $parameters)
    {
        $db = $criteria->getClassMap()->getDb();
        $statement = $criteria->getSqlStatement();
        $statement->setDb($db);
        $statement->setParameters($parameters);
        $this->execute($db, $statement->delete());
    }

    public function processCriteriaUpdate(UpdateCriteria $criteria, $parameters)
    {
        $db = $criteria->getClassMap()->getDb();
        $statement = $criteria->getSqlStatement();
        $statement->setDb($db);
        $statement->setParameters($parameters);
        $this->execute($db, $statement->update());
    }

    public function processCriteriaAsQuery(PersistentCriteria $criteria, $parameters)
    {
        $db = $criteria->getClassMap()->getDb();
        $query = $this->processCriteriaQuery($criteria, $parameters, $db, FALSE);
        return $query;
    }

    public function processCriteriaAsCursor(PersistentCriteria $criteria, $parameters)
    {
        $db = $criteria->getClassMap()->getDb();
        $cursor = $this->processCriteriaCursor($criteria, $parameters, $db, FALSE);
        return $cursor;
    }

    public function processCriteriaAsObjectArray(PersistentCriteria $criteria, $parameters)
    {
        $db = $criteria->getClassMap()->getDb();
        $result = $this->processCriteriaQuery($criteria, $parameters, $db, FALSE)->getResult();
        $columns = $criteria->getColumnAttributes();
        $array = array();
        if (count($result)) {
            foreach ($result as $row) {
                $object = new stdClass();
                foreach ($columns as $key => $column) {
                    $attribute = $criteria->getColumnAlias($column) ?: $column;
                    $object->$attribute = $row[$key];
                }
                $array[] = $object;
            }
        }
        return $array;
    }

    /**
     * Get Criteria
     *
     */
    public static function getCriteria($className = '')
    {
        $criteria = NULL;
        if ($className != '') {
            $manager = PersistentManager::getInstance();
            $classMap = $manager->getClassMap($className);
            $criteria = new RetrieveCriteria($classMap);
        }
        return $criteria;
    }

    public function getRetrieveCriteria(PersistentObject $object, $command = '')
    {
        $classMap = $object->getClassMap();
        $criteria = new RetrieveCriteria($classMap, $command, $this);
        return $criteria;
    }

    public function getDeleteCriteria(PersistentObject $object)
    {
        $classMap = $object->getClassMap();
        $criteria = new DeleteCriteria($classMap, $this);
        $criteria->setTransaction($object->getTransaction());
        return $criteria;
    }

    public function getUpdateCriteria(PersistentObject $object)
    {
        $classMap = $object->getClassMap();
        $criteria = new UpdateCriteria($classMap, $this);
        $criteria->setTransaction($object->getTransaction());
        return $criteria;
    }

    /**
     * Get Connection
     *
     *
     * @param <type> $dbName
     * @return <type>
     */
    public function getConnection($dbName)
    {
        if (($conn = $this->dbConnections[$dbName]) == NULL) {
            $conn = self::$container->getDatabase($dbName);
            $this->dbConnections[$dbName] = $conn;
        }
        return $conn;
    }

    /**
     *  Compatibilidade
     *  Get Value of Attribute
     *
     */
    public function getValue($object, $attribute)
    {
        $map = NULL;
        $cm = $object->getClassMap();
        $db = $this->getConnection($cm->getDatabaseName());
        if (strpos($attribute, '.')) { // attribute come from Association
            $tok = strtok($attribute, ".");
            while ($tok) {
                $nameSequence[] = $tok;
                $tok = strtok(".");
            }
            for ($i = 0; $i < count($nameSequence) - 1; $i++) {
                $name = $nameSequence[$i];
                $object->retrieveAssociation($name);
                $object = $object->$name;
            }
            if ($cm != NULL) {
                $attribute = $nameSequence[count($nameSequence) - 1];
                $value = $object->$attribute;
            }
        } else {
            $value = $object->$attribute;
        }
        return $value;
    }

}
