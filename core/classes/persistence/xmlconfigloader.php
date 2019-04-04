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

class XMLConfigLoader
{

    private $broker;
    private $xmlMaps = array();
    private $classMaps = array();
    private static $location = array();

    public function __construct(PersistentManager $broker)
    {
        $this->broker = $broker;  // factory
    }

    private function getAsArray($object)
    {
        return (is_array($object)) ? $object : array($object);
    }

    public function getLocation($className)
    {
        return $this->location[$className];
    }

    public function getMap($module, $class, $className)
    {
        if (!isset($this->xmlMaps[$className])) {
            $classNameMap = $module . "/models/map/" . $class . '.xml';
            $file = Manager::getAppPath("modules/" . $classNameMap);
            $xmlTree = new MXMLTree($file);
            $tree = $xmlTree->getTree();
            $xml = $xmlTree->getXMLTreeElement($tree);
            $this->xmlMaps[$className] = $xml;
        }
        return $this->xmlMaps[$className];
    }

    public function getClassMap($className, $module = '', $class = '')
    {
        $className = str_replace('\\', '', $className);
        if (isset($this->classMaps[$className])) {
            return $this->classMaps[$className];
        }
        if ($module == '') {
            $module = $this->location[$className]['module'];
            $class = $this->location[$className]['class'];
        } else {
            $this->location[$className]['module'] = $module;
            $this->location[$className]['class'] = $class;
        }
        $xml = $this->getMap($module, $class, $className);


        $database = (string)$xml->databaseName;
        $classMap = new ClassMap($className, $database);
        $classMap->setDatabaseName($database);
        $classMap->setTableName((string)$xml->tableName);

        if (isset($xml->extends)) {
            $classMap->setSuperClassName((string)$xml->extends);
        }

        //$config = $className::config();

        $attributes = $this->getAsArray($xml->attribute);
        foreach ($attributes as $attr) {
            $attributeMap = new AttributeMap((string)$attr->attributeName, $classMap);
            //     $converter = $this->getConverter($attr);

            if (isset($attr->attributeIndex)) {
                $attributeMap->setIndex($attr->attributeIndex);
            }

            $type = isset($attr->columnType) ? strtolower($attr->columnType) : 'string';

            if (($converterName = strtolower($attr->converter->converterName)) != '') {
                if ($converterName == 'timestampconverter') {
                    $type = 'timestamp';
                }
            }

            $attributeMap->setType($type);
            $plataformTypedAttributes = $classMap->getDb()->getPlatform()->getTypedAttributes();
            $attributeMap->setHandled(strpos($plataformTypedAttributes, $type));
            $attributeMap->setColumnName($attr->columnName ?: $attributeName);
            $attributeMap->setAlias($attr->aliasName ?: $attributeName);
            $attributeMap->setKeyType($attr->key ?: 'none');
            $attributeMap->setIdGenerator($attr->idgenerator);

            if ((isset($attr->reference)) && ($classMap->getSuperClassMap() != NULL)) {
                $referenceAttribute = $classMap->getSuperClassMap()->getAttributeMap($attributeName);
                if ($referenceAttribute) {
                    $attributeMap->setReference($referenceAttribute);
                }
            }
            $classMap->addAttributeMap($attributeMap);
        }

        $this->classMaps[$className] = $classMap;

        if (isset($xml->association)) {
            $associations = $this->getAsArray($xml->association);
            $fromClassMap = $classMap;
            foreach ($associations as $association) {
                $associationName = (string)$association->target;
                $toClass = 'business' . $association->toClassModule . $association->toClassName;
                $this->location[$toClass]['module'] = $association->toClassModule;
                $this->location[$toClass]['class'] = $association->toClassName;
                $classPath = Manager::getAppPath("modules/" . $association->toClassModule . '/models/' . $association->toClassName . '.class.php');
                Manager::addAutoloadClass($toClass, $classPath);

                $associationMap = new AssociationMap($classMap, $associationName);
                $associationMap->setToClassName($toClass);

                $associationMap->setDeleteAutomatic($association->deleteAutomatic);
                $associationMap->setSaveAutomatic($association->saveAutomatic);
                $associationMap->setRetrieveAutomatic($association->retrieveAutomatic);
                //$associationMap->setJoinAutomatic($association['joinAutomatic']);
                $autoAssociation = (strtolower($className) == strtolower($toClass));
                if (!$autoAssociation) {
                    $autoAssociation = (strtolower($className) == strtolower(substr($toClass, 1)));
                }

                $associationMap->setAutoAssociation($autoAssociation);
                if (isset($association->indexAttribute)) {
                    $associationMap->setIndexAttribute($association->indexAttribute->indexAttributeName);
                }
                $associationMap->setCardinality($association->cardinality);
                if ($association->cardinality == 'manyToMany') {
                    $associativeClassName = 'business' . $association->associativeClassModule . $association->associativeClassName;
                    $associativeXML = $this->getMap($association->associativeClassModule, $association->associativeClassName, $associativeClassName);
                    $associationMap->setAssociativeTable((string)$associativeXML->tableName);
                } else {
                    $entries = $this->getAsArray($association->entry);
                    $inverse = ($association->inverse == 'true');
                    foreach ($entries as $entry) {
                        $fromAttribute = $inverse ? $entry->toAttribute : $entry->fromAttribute;
                        $toAttribute = $inverse ? $entry->fromAttribute : $entry->toAttribute;
                        $associationMap->addKeys($fromAttribute, $toAttribute);
                    }
                }

                if (isset($association->orderAttribute)) {
                    $order = array();
                    $orderAttributes = $this->getAsArray($association->orderAttribute);
                    foreach ($orderAttributes as $orderAttr) {
                        $ascend = ($orderAttr->orderAttributeDirection == 'ascend');
                        $order[] = array($orderAttr->orderAttributeName, $ascend);
                    }
                    if (count($order)) {
                        $associationMap->setOrder($order);
                    }
                }

                $fromClassMap->putAssociationMap($associationMap);
            }
        }
        return $classMap;
    }

    public function getConverter($attributeNode)
    {
        $converterNode = $attributeNode->converterClass;
        if (!$converterNode) {
            $converterNode = $attributeNode->converter;
            if (!$converterNode) {
                $converter = ConverterFactory::getTrivialConverter();
            } else {
                $name = $converterNode->converterName;
                $converter = $this->broker->getConverter($name);
                if (!$converter) {
                    $parameters = $this->getParameters($converterNode);
                    $factory = new ConverterFactory();
                    $converter = $factory->getConverter($name, $parameters);
                    $this->broker->putConverter($name, $converter);
                }
            }
        } else {
            $name = (string)$converterNode;
            $converter = $this->broker->getConverter($name);
            if (!$converter) {
                $factory = new ConverterFactory();
                $converter = $factory->getConverter($name);
                $this->broker->putConverter($name, $converter);
            }
        }
        return $converter;
    }

    public function getParameters($node = NULL)
    {
        $param = NULL;
        if ($node) {
            $parameters = $this->getAsArray($node->parameter);
            foreach ($parameters as $parameter) {
                $param[$parameter->parameterName] = $parameter->parameterValue;
            }
        }
        return $param;
    }

}
