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

class RetrieveCriteria extends PersistentCriteria
{

    private $command = '';
    private $distinct = FALSE;
    private $forUpdate = FALSE;
    private $range = NULL;
    private $setOperation = array();
    private $linguistic = false;

    public function __construct($classMap, $command = '')
    {
        parent::__construct($classMap);
        $this->command = $command;
        if ($this->command != '') {
            $this->parseCommand();
        }
    }

    /*
     * Parsing command
     */

    private function findStr($target, $source)
    {
        $l = strlen($target);
        $lsource = strlen($source);
        $pos = 0;
        while (($pos < $lsource) && (!$fim)) {
            if ($source[$pos] == "(") {
                $p = $this->findStr(")", substr($source, $pos + 1));
                if ($p > 0) {
                    $pos += $p + 3;
                }
            }
            $fim = ($target == substr($source, $pos, $l));
            if (!$fim) {
                $pos++;
            }
        }
        return ($fim ? $pos : -1);
    }

    protected function parseSqlCommand(&$cmd, $clause, $delimiters)
    {
        if (substr($cmd, 0, strlen($clause)) != $clause) {
            return false;
        }
        $cmd = substr($cmd, strlen($clause));
        $n = count($delimiters);
        $i = 0;
        $pos = -1;
        while (($pos < 0) && ($i < $n))
            $pos = $this->findStr($delimiters[$i++], $cmd);
        if ($pos > 0) {
            $r = substr($cmd, 0, $pos);
            $cmd = substr($cmd, $pos);
        }
        return $r;
    }

    protected function parseCommand()
    {
        $command = trim($this->command) . " #";
        $command = preg_replace("/(?i)select /", "select ", $command);
        $command = preg_replace("/(?i) from /", " from ", $command);
        $command = preg_replace("/(?i) where /", " where ", $command);
        $command = preg_replace("/(?i) order by /", " order by ", $command);
        $command = preg_replace("/(?i) group by /", " group by ", $command);
        $command = preg_replace("/(?i) having /", " having ", $command);
        $command = preg_replace("/(?i) join /", " join ", $command);
        // attributes
        $this->select($this->parseSqlCommand($command, "select", array("from", "where", "group by", "order by", "#")));
        $from = trim($this->parseSqlCommand($command, "from", array("where", "group by", "order by", "#")));
        if ($from != '') {
            //if ($this->findStr(' join ', $command) < 0) {
            if (strpos($from, ' join ') === false) {
                // from
                $this->from($from);
            } else {
                // join
                $this->join($from);
            }
        }
        // where
        $where = trim($this->parseSqlCommand($command, "where", array("group by", "order by", "#")));
        if ($where != '') {
            $this->where($where);
        }
        // groupby
        $groupby = trim($this->parseSqlCommand($command, "group by", array("having", "order by", "#")));
        if ($groupby != '') {
            $this->groupBy($groupby);
        }
        // having
        $having = trim($this->parseSqlCommand($command, "having", array("order by", "#")));
        if ($having != '') {
            $this->having($having);
        }
        // order by
        $orderby = trim($this->parseSqlCommand($command, "order by", array("#")));
        if ($orderby != '') {
            $this->orderBy($orderby);
        }
    }

    public function getSql()
    {
        $query = $this->asQuery();
        return $query->getCommand();
    }

    public function getSqlStatement()
    {
        $statement = new \database\MSQL();

        if (count($this->columns) == 0) {
            $this->select('*');
        }
        $sqlColumns = array();
        foreach ($this->columns as $column) {
            $sqlColumns[] = $this->getOperand($column)->getSql();
        }
        $columns = implode(',', $sqlColumns);
        $statement->setColumns($columns, $this->distinct);


        if (($where = $this->whereCondition->getSql()) != '') {
            $statement->setWhere($where);
        }

        if (count($this->groups)) {
            foreach ($this->groups as $group) {
                $sqlGroups[] = $this->getOperand($group)->getSqlGroup();
            }
            $groups = implode(',', $sqlGroups);
            $statement->setGroupBy($groups);
        }

        if (($having = $this->havingCondition->getSql()) != '') {
            $statement->setHaving($having);
        }

        $this->includeOrderStatment($statement);

        if ($n = count($this->tableCriteria)) {
            for ($i = 0; $i < $n; $i++) {
                $tables .= (($i > 0 ? ", " : "") . '(' . $this->tableCriteria[$i][0] . ')' . ' ' . $this->tableCriteria[$i][1]);
            }
            $statement->setTables($tables);
        }

        $hasJoin = false;
        $joins = $this->getForcedJoin();
        if (count($joins)) {
            $hasJoin = true;
            foreach ($joins as $join) {
                $statement->join[] = $join;
            }
        }
        $joins = $this->getAssociationsJoin();
        if (count($joins)) {
            $hasJoin = true;
            foreach ($joins as $join) {
                $statement->join[] = $join;
            }
        }
        if (!$hasJoin) {
            if (count($this->classes)) {
                $sqlTables = array();
                foreach ($this->classes as $class) {
                    $sqlTables[] = $this->getTableName($class[0]) . ' ' . $class[1];
                }
                $tables = implode(',', $sqlTables);
                $statement->setTables($tables);
            }
        }
        // Set parameters to the select statement
        if (!is_null($this->parameters)) {
            $statement->setParameters($this->parameters);
        }
        // Add a range clause to the select statement
        if (!is_null($this->range)) {
            $statement->setRange($this->range);
        }
        // Add a FOR UPDATE clause to the select statement
        if ($this->forUpdate) {
            $statement->setForUpdate(TRUE);
        }
        // Add Set Operations
        if (count($this->setOperation)) {
            foreach ($this->setOperation as $s) {
                $statement->setSetOperation($s[0], $s[1]->getSqlStatement());
            }
        }
        return $statement;
    }

    private function includeOrderStatment(\database\MSQL $statement)
    {
        if (count($this->orders)) {
            $sqlOrders = array();
            foreach ($this->orders as $order) {
                $sqlOrders[] = $this->processOrder($order);
            }
            $orders = implode(',', $sqlOrders);
            $statement->setOrderBy($orders);
        }
    }

    private function processOrder($order)
    {
        $parts = explode(' ', $order);
        return trim($this->getOperand($parts[0])->getSqlOrder() . ' ' . $parts[1] . ' ' . $parts[2] . ' ' . $parts[3]);
    }

    public function distinct($distinct = TRUE)
    {
        $this->distinct = $distinct;
        return $this;
    }

    public function range()
    {
        $numargs = func_num_args();
        if ($numargs == 1) {
            $this->range = func_get_arg(0);
        } elseif ($numargs == 2) {
            $page = func_get_arg(0);
            $rows = func_get_arg(1);
            $this->range = new MRange($page, $rows);
        }
        return $this;
    }

    public function forUpdate($forUpdate = FALSE)
    {
        $this->forUpdate = $forUpdate;
        return $this;
    }

    public function select()
    {
        if ($numargs = func_num_args()) {
            foreach (func_get_args() as $arg) {
                $attributes = explode(',', $arg);
                if (count($attributes)) {
                    foreach ($attributes as $attribute) {
                        $attribute = trim($attribute);
                        if ($attribute == '*') {
                            $classMap = $this->classMap;
                            do {
                                for ($i = 0; $i < $classMap->getSize(); $i++) {
                                    $am = $classMap->getAttributeMap($i);
                                    $this->columns[] = $am->getName();
                                }
                                $classMap = $classMap->getSuperClassMap();
                            } while ($classMap != NULL);
                        } else {
                            $parts = explode(' as ', $attribute);
                            $this->columns[] = trim($parts[0]) . (($label = trim($parts[1])) ? ' as ' . $label : '');
                        }
                    }
                } else {
                    $this->columns[] = $arg;
                }
            }
        }
        return $this;
    }

    public function from()
    {
        if ($numargs = func_num_args()) {
            foreach (func_get_args() as $arg) {
                $classes = explode(',', $arg);
                if (count($classes)) {
                    $this->addMultipleClasses($classes);
                } else {
                    $this->addClass(trim($arg));
                }
            }
        }
        return $this;
    }

    public function join($c1OrJoin, $c2 = '', $condition = '', $joinType = 'INNER')
    {
        if (($numargs = func_num_args()) > 1) {
            $classes[] = $c1 = func_get_arg(0);
            $classes[] = $c2 = func_get_arg(1);
        } else {
            $join = preg_replace("/(?i) inner /", " inner ", $c1OrJoin);
            $join = preg_replace("/(?i) left /", " left ", $join);
            $join = preg_replace("/(?i) right /", " right ", $join);
            $join = preg_replace("/(?i) outer /", " outer ", $join);
            $join = preg_replace("/(?i) full /", " full ", $join);
            if (preg_match_all('/(.*?)(( inner | left | right | outer | full )?join)(.*)on(.*)/', $join, $matches)) {
                $classes[] = $c1 = trim($matches[1][0]);
                $classes[] = $c2 = trim($matches[4][0]);
                $condition = trim($matches[5][0]);
                $joinType = trim($matches[3][0]) ?: 'inner';
            }
        }
        $this->addMultipleClasses($classes);
        $this->joins[] = array($c1, $c2, $condition, $joinType);
        return $this;
    }

    private function addMultipleClasses(array $classes)
    {
        foreach ($classes as $class) {
            $parts = explode(' ', $class);
            $className = trim($parts[0]);
            $this->addClass(trim($className), trim($parts[1]));
        }
    }


    public function autoAssociation($alias1, $alias2, $condition = '', $joinType = 'INNER')
    {
        $this->setAutoAssociation($alias1, $alias2, $condition, $joinType);
        return $this;
    }

    public function associationAlias($associationName, $alias)
    {
        return $this->setAssociationAlias($associationName, $alias);
    }

    public function associationType($associationName, $joinType)
    {
        return $this->setAssociationType($associationName, $joinType);
    }

    public function where($op1, $operator = '', $op2 = NULL)
    {
        $this->whereCondition->and_($op1, $operator, $op2);
        return $this;
    }

    public function and_($op1, $operator = '', $op2 = NULL)
    {
        return $this->where($op1, $operator, $op2);
    }

    public function or_($op1, $operator = '', $op2 = NULL)
    {
        $this->whereCondition->or_($op1, $operator, $op2);
        return $this;
    }

    public function condition()
    {
        if ($numargs = func_num_args()) {
            $this->addMultiCriteria(func_get_args());
        }
        return $this;
    }

    public function groupBy()
    {
        if ($numargs = func_num_args()) {
            foreach (func_get_args() as $arg) {
                $arg = trim($arg);
                if ($arg) {
                    $this->groups[] = $arg;
                }
            }
        }
        return $this;
    }

    public function having($op1, $operator = '', $op2 = NULL)
    {
        $this->havingCondition->and_($op1, $operator, $op2);
        return $this;
    }

    public function havingAnd($op1, $operator = '', $op2 = NULL)
    {
        return $this->having($op1, $operator, $op2);
    }

    public function havingOr_($op1, $operator = '', $op2 = NULL)
    {
        $this->havingCondition->or_($op1, $operator, $op2);
        return $this;
    }

    public function orderBy()
    {
        if ($numargs = func_num_args()) {
            foreach (func_get_args() as $arg) {
                $orders = explode(',', $arg);
                if (count($orders)) {
                    foreach ($orders as $order) {
                        $this->orders[] = trim($order);
                    }
                } else {
                    $this->orders[] = $arg;
                }
            }
        }
        return $this;
    }

    /**
     * Função para permitir a adição de parâmetros progressivamente. Criada pela limitação da função "parameters".
     * Essa função leva em conta que os parametros podem ser um array, um objeto ou um valor escalar.
     * @param $name Nome do parametro
     * @param string $value Valor do parametro
     * @return $this
     */
    public function addParameter($name, $value = '')
    {
        if (null === $this->parameters) {
            $this->parameters = [];
        }

        if (is_scalar($this->parameters)) {
            $this->parameters = array($this->parameters);
        }

        if (is_array($this->parameters)) {
            $this->parameters[$name] = $value;
        } elseif (is_object($this->parameters)) {
            $this->parameters->$name = $value;
        }

        return $this;
    }

    public function parameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getParameters()
    {
        return $this->parameters ?: [];
    }

    public function setOperation($operation, $criteria)
    {
        $this->setOperation[] = array($operation, $criteria);
    }

    public function union($criteria)
    {
        $this->setOperation('UNION', $criteria);
        return $this;
    }

    public function intersect($criteria)
    {
        $this->setOperation('INTERSECT', $criteria);
        return $this;
    }

    public function minus($criteria)
    {
        $this->setOperation('MINUS', $criteria);
        return $this;
    }

    public function ignoreAccentuation()
    {
        $this->linguistic = true;
        return $this;
    }

    /**
     * @return \database\MQuery
     */
    public function asQuery($parameters = null)
    {
        if (func_num_args() == 0) {
            $parameters = $this->parameters;
        } elseif (func_num_args() > 1) {
            $parameters = func_get_args();
        }

        $query = $this->manager->processCriteriaAsQuery($this, $parameters);

        if ($this->linguistic) {
            $query->ignoreAccentuation();
        }

        return $query;
    }

    public function asCursor($parameters = null)
    {
        return $this->manager->processCriteriaAsCursor($this, $parameters);
    }

    public function asObjectArray($parameters = null)
    {
        return $this->manager->processCriteriaAsObjectArray($this, $parameters);
    }

    /*
     * Compatibilidade
     */

    public function setDistinct($value)
    {
        $this->distinct($value);
    }

}
