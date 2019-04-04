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

class PersistentCriteria extends BaseCriteria
{

    protected $columns = array();
    protected $classes = array();
    protected $aliases = array();
    //protected $tables = array();
    protected $whereCondition = NULL;
    protected $groups = array();
    protected $havingCondition;
    protected $orders = array();
    protected $joins = array();
    protected $associations = array();
    protected $autoAssociation;
    protected $parameters = NULL;
    protected $classMap = NULL;
    protected $alias = '';
    protected $maps = array(); // array of classMaps
    protected $manager = NULL;
    protected $tableCriteria = array();
    protected $tableCriteriaColumn = array();

    public function __construct($classMap = NULL)
    {
        $this->manager = PersistentManager::getInstance();
        $this->setClassMap($classMap);
        // Fill tables with tableMaps
        // Create CriteriaCondition for the WHERE part of this criteria
        $this->whereCondition = $this->getNewCondition();
        //$this->whereCondition->setCriteria($this);
        // Create condition for the HAVING part of this criteria
        $this->havingCondition = $this->getNewCondition();
        //$this->havingCondition->setCriteria($this);
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function setClassMap($classMap = NULL)
    {
        if ($classMap) {
            $this->classMap = $classMap;
            do {
                $this->addClassMap($classMap);
                $classMap = $classMap->getSuperClassMap();
            } while ($classMap != NULL);
        }
    }

    public function getClassMap($className = '')
    {
        if ($className == '') {
            return $this->classMap;
        }
        return $this->getMap($className);
    }

    public function getNewCondition()
    {
        $condition = new ConditionCriteria();
        $condition->setCriteria($this);
        return $condition;
    }

    public function getWhereCondition()
    {
        return $this->whereCondition;
    }

    public function getHavingCondition()
    {
        return $this->havingCondition;
    }

    public function getMap($className)
    {
//        $className = trim(strtolower($className));
        $className = trim($className);
        return $this->maps[$className];
    }

    public function addClass($className, $alias = '', $classMap = NULL)
    {
        $className = trim($className);
        $fullClassName = $className;
        if (strrpos($className, '\\') === false) {
//            $className = strtolower($className);
            $fullClassName = $this->classMap->getNamespace() . '\\' . $className;
        }
        if ((!isset($this->classes[$fullClassName])) || ($alias != '')) {
            $this->classes[$fullClassName] = array($fullClassName, $alias);
        }
        if (!$classMap) {
            $classMap = $this->manager->getClassMap($fullClassName);
        }
        if ($alias != '') {
            $this->registerAlias($alias, $classMap);
        }
        if ($classMap) {
            $this->maps[$className] = $classMap;
            $this->maps[$fullClassName] = $classMap;
        }
        return $this;
    }

    public function addClassMap($classMap, $alias = '')
    {
        $className = $classMap->getName();
        $this->addClass($className, $alias, $classMap);
    }

    public function registerAlias($alias, $class = NULL)
    {
        if ($class instanceof ClassMap) {
            $this->aliases[$alias] = $class;
        } else {
            $classMap = $this->manager->getClassMap($class);
            $this->aliases[$alias] = $classMap;
        }
    }

    public function getAlias($className = '')
    {
        if ($className != '') {
            $className = trim(strtolower($className));
            $fullClassName = $className;
            if ((strrpos($className, '\\') === false) && (strrpos($className, 'business') === false)) {
                $fullClassName = $this->classMap->getNamespace() . '\\' . $className;
            }
            $alias = $this->classes[$fullClassName][1];
        } else {
            $alias = $this->alias;
        }
        return $alias;
    }

    public function setAlias($alias, $class = NULL)
    {
        if ($class == NULL) {
            $class = $this->classMap;
            $this->alias = $alias;
            $this->classes[$class->getName()] = array($class->getName(), $alias);
        }
        $this->registerAlias($alias, $class);
        return $this;
    }

    public function getMapFromAlias($alias)
    {
        return $this->aliases[$alias];
    }

    public function isAlias($name)
    {
        return (isset($this->aliases[$name]));
    }

    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Merge aliases from outer criteria.
     * @param <type> $criteria
     */
    public function mergeAliases($criteria)
    {
        $aliases = $criteria->getAliases();
        if (count($aliases)) {
            foreach ($aliases as $alias => $classMap) {
                if (!isset($this->alias[$alias])) {
                    $this->aliases[$alias] = $classMap;
                }
            }
        }
    }

    public function setAssociationAlias($associationName, $alias)
    {
        $association = $this->getAssociation($associationName);
        if ($association == NULL) {
            $association = $this->addAssociation($associationName);
        }
        $association->setAlias($alias);
        $classMap = $association->getAssociationMap()->getToClassMap();
        $this->setAlias($alias, $classMap);
        return $this;
    }

    public function setAssociationType($associationName, $joinType)
    {
        $association = $this->getAssociation($associationName);
        if ($association == NULL) {
            $association = $this->addAssociation($associationName);
        }
        $association->setJoinType($joinType);
        return $this;
    }

    public function setAutoAssociation($alias1, $alias2, $condition = '', $joinType = 'INNER')
    {
        $className = $this->classMap->getName();
        $this->setAlias($alias1);
        $this->addClass($className, $alias1);
        $this->addClass($className, $alias2);
        $this->autoAssociation = array($className . ' ' . $alias1, $className . ' ' . $alias2, $condition, $joinType);
        return $this;
    }

    public function setReferenceAlias($alias)
    {
        $className = $this->classMap->getName();
        $this->addClass($className, $alias);
        return $this;
    }

    public function getAssociationsJoin()
    {
        // Build a join array to sql statement
        $join = array();

        // Inheritance associations
        $classMap = $this->classMap;

        do {
            for ($i = 0; $i < $classMap->getReferenceSize(); $i++) {
                $attributeMap = $classMap->getReferenceAttributeMap($i);
                $tableName1 = $attributeMap->getClassMap()->getTableName();
                $tableAlias1 = $this->tables[$tableName1];
                $tableName1 .= ' ' . $tableAlias1;
                $referenceAttributeMap = $attributeMap->getReference();
                $tableName2 = $referenceAttributeMap->getClassMap()->getTableName();
                $tableAlias2 = $this->tables[$tableName2];
                $tableName2 .= ' ' . $tableAlias2;
                $condition = $attributeMap->getFullyQualifiedName($tableAlias1) . "=" . $referenceAttributeMap->getFullyQualifiedName($tableAlias2);
                $join[] = array($tableName1, $tableName2, $condition, 'INNER');
            }
            $classMap = $classMap->getSuperClassMap();
        } while ($classMap != NULL);

        // AutoAssociation
        if ($this->autoAssociation) {
            $class1 = $this->getOperand($this->autoAssociation[0])->getSql();
            $class2 = $this->getOperand($this->autoAssociation[1])->getSql();
            $condition = $this->getOperand($this->autoAssociation[2])->getSql();
            $join[] = array($class1, $class2, $condition, $this->autoAssociation[3]);
        }

        // Associations
        if (count($this->associations)) {
            foreach ($this->associations as $associationCriteria) {
                $associationJoins = $associationCriteria->getJoin();
                if (count($associationJoins)) {
                    foreach ($associationJoins as $associationJoin) {
                        $join[] = $associationJoin;
                    }
                }
            }
        }

        return $join;
    }

    public function getForcedJoin()
    {
        // Build a join array to sql statement
        $join = array();

        // Forced joins
        if (count($this->joins)) {
            foreach ($this->joins as $forcedJoin) {
                $class1 = $this->getOperand($forcedJoin[0])->getSql();
                $class2 = $this->getOperand($forcedJoin[1])->getSql();
                $condition = $this->getOperand($forcedJoin[2])->getSql();
                $join[] = array($class1, $class2, $condition, $forcedJoin[3]);
            }
        }

        return $join;
    }

    public function getAttributeMap(&$attribute)
    {
        if ($this->checkAttributesToSkip($attribute)) {
            return;
        }
        $attributeMap = null;
        $classMap = $this->classMap;
        $tokens = preg_split('/[.]+/', $attribute);
        if (count($tokens) > 1) { // has associations
            for ($i = 0; $i < count($tokens) - 1; $i++) {
                $name = $tokens[$i];
                if ($this->isAlias($name)) {
                    $classMap = $this->getMapFromAlias($name);
                } else {
                    $existentMap = $this->getMap($name);
                    if ($existentMap instanceof ClassMap) {
                        $classMap = $existentMap;
                        break;
                    } else {
                        $currentClassMap = $classMap;
                        $association = $this->getAssociation($name, $classMap)
                            ?: $this->addAssociation($name, 'INNER', $classMap);
                        if ($association == NULL) {
                            return $association;
                        }
                        $associationMap = $association->getAssociationMap();
                        // If association map is NULL something wrong with names
                        if (isset($associationMap)) {
                            $classMap = $associationMap->getToClassMap();
                        } else {
                            $classMap = $this->getMap($name);
                            if (!isset($classMap)) {
                                throw new EPersistenceException($currentClassMap->getName() . ' Invalid association/alias name [' . $name . '] in attribute [' . $attribute . ']');
                            }
                        }
                    }
                }
            }

            if ($classMap != NULL) {
                $attribute = array_pop($tokens);
                $attributeMap = $classMap->getAttributeMap($attribute, TRUE);
                if (($classMap == NULL) || ($this->isAlias($name))) {
                    $attribute = $name . '.' . $attribute;
                }
            }
        } else {
            $attributeMap = $classMap->getAttributeMap($attribute, TRUE);
            if (($classMap != NULL) && ($this->getAlias())) {
                $attribute = $this->getAlias() . '.' . $attribute;
            }
        }
        return $attributeMap;
    }

    private function checkAttributesToSkip($attribute)
    {
        return in_array(trim($attribute), ['', '=', '?', '(', ')', 'and', 'or', 'not']);
    }

    /*
     * Criteria clauses
     */

    public function addColumnAttribute($attribute, $label = '')
    {
        $attribute = trim($attribute);
        if ($attribute == '*') {
            $classMap = $this->classMap;
            for ($i = 0; $i < $classMap->getSize(); $i++) {
                $am = $classMap->getAttributeMap($i);
                $this->columns[] = $am->getName() . ($label = $am->getAlias() ? ' as ' . $label : '');
            }
        } else {
            $this->columns[] = $attribute . ($label ? ' as "' . $label . '"' : '');
        }
    }

    public function addCriteria($op1, $operator = '', $op2 = NULL)
    {
        $this->whereCondition->and_($op1, $operator, $op2);
    }

    public function addOrCriteria($op1, $operator = '', $op2 = NULL)
    {
        $this->whereCondition->or_($op1, $operator, $op2);
    }

    private function convertMultiCriteria($condition, &$criteriaCondition)
    {
        if (is_array($condition)) {
            foreach ($condition as $c) {
                if (is_array($c[1])) {
                    $cc = new ConditionCriteria();
                    $this->convertMultiCriteria($c[1], $cc);
                    $criteriaCondition->add($cc, $c[0]);
                } else {
                    $base = new PersistentCondition($c[1], $c[2], $c[3]);
                    $base->setCriteria($this);
                    $criteriaCondition->add($base, $c[0]);
                }
            }
        }
    }

    public function addMultiCriteria($condition)
    {
        $this->convertMultiCriteria($condition, $this->whereCondition);
    }

    /**
     * A criteria used at FROM clause.
     * @param <type> $criteria
     * @param <type> $alias
     */
    public function tableCriteria($criteria, $alias)
    {
        $sql = $criteria->getSqlStatement();
        $sql->setDb($this->getClassMap()->getDb());
        $this->tableCriteria[] = array($sql->select()->getCommand(), $alias);
        return $this;
    }

    public function joinCriteria($criteria, $condition, $joinType = 'INNER')
    {
        $this->joins[] = array($this->classMap->getName(), $criteria, $condition, $joinType);
    }

    public function getCriteria($op1, $operator = '', $op2 = NULL)
    {
        $operand1 = $this->getOperand($op1);
        $operand2 = $this->getOperand($op2);
        $criteria = BaseCriteria::getCondition($operand1, $operator, $operand2);
        return $criteria;
    }

    public function addGroupAttribute($attribute)
    {
        $this->groups[] = $attribute;
    }

    public function addHavingCriteria($op1, $operator, $op2)
    {
        $this->havingCondition->addCriteria($this->getCriteria($op1, $operator, $op2));
    }

    public function addOrHavingCriteria($op1, $operator, $op2)
    {
        $this->havingCondition->addOrCriteria($this->getCriteria($op1, $operator, $op2));
    }

    public function addOrderAttribute($attribute, $ascend = TRUE)
    {
        $this->orders[] = $attribute . ($ascend ? ' ASC' : ' DESC');
    }

    public function addParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    public function addParameter($value, $name = '')
    {
        if ($name != '') {
            $this->parameters[$name] = $value;
        } else {
            $this->parameters[] = $value;
        }
    }

    /*
     * Associations methods
     */

    public function getAssociation($associationName, $classMap = NULL)
    {
        if ($classMap == NULL) {
            $classMap = $this->classMap;
        }
        $associationCriteria = NULL;
        foreach ($this->associations as $a) {
            $classMapName = $classMap->getName();
            $prefixedAssociationName = $classMapName . '.' . $associationName;
            /*
            if ($classMap != NULL) { // classMap definido
                $classMapName = $classMap->getName();
                if ($classMapName != $this->classMap->getName()) { // não é o classMap corrente
                    $name =  $classMapName . '.' . $associationName;
                } else {
                    $name =  $associationName; // classMap corrente
                }
            } else {
                $name =  $associationName; // classMap corrente
            }
             * 
             */
            if (($a->getName() == $prefixedAssociationName) || ($a->getAlias() == $associationName)) {
                $associationCriteria = $a;
            }
        }
        return $associationCriteria;
    }

    public function addAssociation($name, $joinType = 'INNER', $classMap = NULL)
    {
        if ($classMap == NULL) {
            $classMap = $this->classMap;
        } else {
            $this->addClassMap($classMap);
        }
        $tokens = preg_split('/[.]+/', $name);
        if (count($tokens) > 1) { // associação indireta
            for ($i = 0; $i < count($tokens); $i++) {
                $associationName = $tokens[$i];
                // acrescenta um prefixo, para evitar colisão de nomes de associações
                $prefixedAssociationName = $classMap->getName() . '.' . $associationName;
                $associationCriteria = $this->getAssociation($prefixedAssociationName, $classMap);
                if ($associationCriteria == NULL) {
                    $associationMap = $classMap->getAssociationMap($associationName);
                    //$a = $this->addAssociationCriteria((($i > 0) ? $name : '') . $associationName, $associationMap);
                    $a = $this->addAssociationCriteria($prefixedAssociationName, $associationMap);
                    $classMap = $associationMap->getToClassMap();
                } else {
                    $associationMap = $classMap->getAssociationMap($associationName);
                    $classMap = $associationMap->getToClassMap();
                    $a = $associationCriteria;
                }
            }
            return $a;
        } else {  // associação direta
            $associationMap = $classMap->getAssociationMap($name);
            $name = $classMap->getName() . '.' . $name;
            $associationCriteria = $associationMap ? $this->addAssociationCriteria($name, $associationMap, $joinType) : null;
            while ($associationMap != NULL) { // se a classe for uma subClasse, adiciona a associação com a superClasse
                $classMap = $associationMap->getToClassMap();
                $associationMap = $classMap->getSuperAssociationMap();
                if ($associationMap != NULL) {
                    $name = $associationMap->getName();
                    $name = $classMap->getName() . '.' . $name;
                    $this->addAssociationCriteria($name, $associationMap, 'INNER');
                }
            }
            return $associationCriteria;
        }
    }

    public function addAssociationCriteria($name, $associationMap, $joinType = 'INNER')
    {
        $this->associations[$name] = new AssociationCriteria($name, $this, $joinType);
        $this->associations[$name]->setAssociationMap($associationMap);
        return $this->associations[$name];
    }

    /*
     * Retrieve methods
     */

    public function retrieveAsQuery($parameters = null)
    {
        return $this->manager->processCriteriaAsQuery($this, $parameters);
    }

    public function retrieveAsCursor($parameters = null)
    {
        return $this->manager->processCriteriaAsCursor($this, $parameters);
    }

    public function retrieveAsProxyQuery($parameters = null)
    {
        return $this->manager->processCriteriaAsProxyQuery($this, $parameters);
    }

    public function retrieveAsProxyCursor($parameters = null)
    {
        return $this->manager->processCriteriaAsProxyCursor($this, $parameters);
    }

}
