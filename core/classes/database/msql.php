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

namespace database;

class MSQL
{

    /**
     * Attribute Description.
     */
    public $db;
    public $platform;

    /**
     * Attribute Description.
     */
    public $distinct;

    /**
     * Attribute Description.
     */
    public $columns;

    /**
     * Attribute Description.
     */
    public $tables;

    /**
     * Attribute Description.
     */
    public $where;

    /**
     * Attribute Description.
     */
    public $groupBy;

    /**
     * Attribute Description.
     */
    public $having;

    /**
     * Attribute Description.
     */
    public $orderBy;

    /**
     * Attribute Description.
     */
    public $forUpdate;

    /**
     * Attribute Description.
     */
    public $join;

    /**
     * Attribute Description.
     */
    public $parameters;

    /**
     * Attribute Description.
     */
    public $paramType;

    /**
     * Attribute Description.
     */
    public $command;

    /**
     * Attribute Description.
     */
    public $range;

    /**
     * Attribute Description.
     */
    public $setOperation;

    /**
     * Attribute Description.
     */
    public $stmt;

    public function __construct($columns = '', $tables = '', $where = '', $orderBy = '', $groupBy = '', $having = '', $forUpdate = false)
    {
        $this->clear();
        $this->setColumns($columns);
        $this->setTables($tables);
        $this->setWhere($where);
        $this->setGroupBy($groupBy);
        $this->setHaving($having);
        $this->setOrderBy($orderBy);
        $this->setForUpdate($forUpdate);
    }

    private function _getTokens($string, &$array)
    {
        if ($string == '') {
            return;
        }

        $source = $string . ',';
        $tok = '';
        $l = strlen($source);
        $can = 0;

        for ($i = 0; $i < $l; $i++) {
            $c = $source{$i};

            if (!$can) {
                if ($c == ',') {
                    $tok = trim($tok);
                    $array[$tok] = $tok;
                    $tok = '';
                } else {
                    $tok .= $c;
                }
            } else {
                $tok .= $c;
            }

            if ($c == '(')
                $can++;

            if ($c == ')')
                $can--;
        }
    }

    private function _getJoin()
    {
        $cond = '';
        if (is_array($this->join)) {
            foreach ($this->join as $join) {
                if ($cond != '') {
                    $cond = "($cond " . $join[3] . " JOIN $join[1] ON ($join[2]))";
                } else {
                    $cond = "($join[0] " . $join[3] . " JOIN $join[1] ON ($join[2]))";
                }
            }
        } else {
            $cond = $this->join;
        }
        $this->setTables($cond);
    }

    private function _getSetOperation()
    {
        $command = '';
        foreach ($this->setOperation as $s) {
            $s[1]->setDB($this->db);
            $command .= ' ' . $this->db->getPlatform()->getSetOperation($s[0]) . ' (' . $s[1]->select()->getCommand() . ')';
        }
        return $command;
    }

    public function setCommand($command)
    {
        $this->command = $command;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function setDb(MDatabase $db)
    {
        $this->db = $db;
        $this->platform = $db->getPlatForm();
    }

    public function setColumns($string, $distinct = false)
    {
        $this->_getTokens($string, $this->columns);
        $this->distinct = $distinct;
    }

    public function setTables($string)
    {
        $this->_getTokens($string, $this->tables);
    }

    public function setGroupBy($string)
    {
        $this->_getTokens($string, $this->groupBy);
    }

    public function setOrderBy($string)
    {
        $this->_getTokens($string, $this->orderBy);
    }

    public function setWhere($string)
    {
        $this->where .= (($this->where != '') && ($string != '') ? " and " : "") . $string;
    }

    public function setWhereAnd($string)
    {
        $this->where .= (($this->where != '') && ($string != '') ? " and " : "") . $string;
    }

    public function setWhereOr($string)
    {
        $this->where .= (($this->where != '') && ($string != '') ? " or " : "") . $string;
    }

    public function setHaving($string)
    {
        $this->having .= (($this->having != '') && ($string != '') ? " and " : "") . $string;
    }

    public function setHavingAnd($string)
    {
        $this->having .= (($this->having != '') && ($string != '') ? " and " : "") . $string;
    }

    public function setHavingOr($string)
    {
        $this->having .= (($this->having != '') && ($string != '') ? " or " : "") . $string;
    }

    public function setJoin($table1, $table2, $cond, $type = 'INNER')
    {
        $this->join[] = array(
            $table1,
            $table2,
            $cond,
            $type
        );
    }

    public function setLeftJoin($table1, $table2, $cond)
    {
        $this->setJoin($table1, $table2, $cond, 'LEFT');
    }

    public function setRightJoin($table1, $table2, $cond)
    {
        $this->setJoin($table1, $table2, $cond, 'RIGHT');
    }

    public function setForUpdate($forUpdate = false)
    {
        $this->forUpdate = $forUpdate;
    }

    function setSetOperation($operation, MSQL $sql)
    {
        $this->setOperation[] = array(
            $operation,
            $sql
        );
    }

    private function _prepareParameters()
    {
        if ($this->parameters === NULL) {
            return;
        }
        if (strpos($this->command, '?')) {
            $i = $pos = 0;
            while (($pos = strpos($this->command, '?', $pos + 1)) !== false) {
                $pos_array[$i] = $pos;
                $i++;
            }
            if ($i != count($this->parameters)) {
                //mdump("[Error] SQL PREPARE: Parâmetros inconsistentes! SQL: {$this->command}");
            }
            if ($i > 0) {
                $sqlText = '';
                $p = 0;
                $parameters = array();
                foreach ($pos_array as $i => $pos) {
                    $param = $this->parameters[$i];
                    if (\is_array($param)) { // informado o tipo do parâmetro: array(param,type)
                        $param = $param[0];
                        $this->paramType[$i] = $param[1];
                    }
                    $param = (\is_null($param) || !isset($param) ? NULL : $param);
                    if (\is_string($param) && ($param{0} == ':')) {
                        $textParam = substr($param, 1);
                    } else {
                        $textParam = '?';
                        $parameters[] = $param;
                    }
                    $sqlText .= substr($this->command, $p, $pos - $p) . $textParam;
                    $p = $pos + 1;
                }
                $sqlText .= substr($this->command, $p);
                $this->command = $sqlText;
                $this->parameters = $parameters;
            }
        }
    }

    public function prepare()
    {
        $connection = $this->db->getConnection();
        $this->_prepareParameters();
        $this->stmt = $connection->prepare($this->command);
        return $this;
    }

    public function bind()
    {
        if ($this->parameters === NULL) {
            return;
        }
        if (count($this->parameters)) {
            foreach ($this->parameters as $i => $param) {
                if (is_numeric($i)) {
                    $this->bindValue($i + 1, $param, $this->paramType[$i]);
                } else {
                    $this->bindValue($i, $param, $this->paramType[$i]);
                }
            }
        }
        return $this;
    }

    public function bindValue($name, $value, $type = null)
    {
        $bindingType = null;
        if (($type !== null) || (is_object($value))) {
            $value = $this->platform->convertToDatabaseValue($value, $type, $bindingType);
        }
        return $this->stmt->bindValue($name, $value, $bindingType);
    }

    public function insert($parameters = null)
    {
        $this->setParameters($parameters);
        $sqlText = 'INSERT INTO ' . implode($this->tables, ',') . ' ( ' . implode($this->columns, ',') . ' ) VALUES ( ';

        for ($i = 0; $i < count($this->columns); $i++) {
            $par[] = '?';
        }

        $sqlText .= implode($par, ',') . ' )';
        $this->command = $sqlText;

        $this->prepare();
        $this->bind();
        return $this;
    }

    public function insertFrom($sql)
    {
        $sqlText = 'INSERT INTO ' . implode($this->tables, ',') . ' ( ' . implode($this->columns, ',') . ' ) ';
        $sqlText .= $sql;
        $this->command = $sqlText;
        return $this;
    }

    public function delete($parameters = null)
    {
        $this->setParameters($parameters);
        $sqlText = 'DELETE FROM ' . implode($this->tables, ',');
        $sqlText .= ' WHERE ' . $this->where;
        $this->command = $sqlText;

        $this->prepare();
        $this->bind();
        return $this;
    }

    public function update($parameters = null)
    {
        $this->setParameters($parameters);
        $sqlText = 'UPDATE ' . implode($this->tables, ',') . ' SET ';

        foreach ($this->columns as $c)
            $par[] = $c . '= ?';

        $sqlText .= implode($par, ',');

        $sqlText .= ' WHERE ' . $this->where;
        $this->command = $sqlText;

        $this->prepare();
        $this->bind();
        return $this;
    }

    public function select($parameters = null)
    {
        $this->setParameters($parameters);
        $sqlText = $this->command;
        if ($sqlText == '') {

            if ($this->join != NULL) {
                $this->_getJoin();
            }

            $sqlText = 'SELECT ' . ($this->distinct ? 'DISTINCT ' : '') . implode($this->columns, ',');

            if (count($this->tables)) {
                $sqlText .= ' FROM   ' . implode($this->tables, ',');
            }

            if ($this->where != '') {
                $sqlText .= ' WHERE ' . $this->where;
            }

            if (count($this->groupBy)) {
                $sqlText .= ' GROUP BY ' . implode($this->groupBy, ',');
            }

            if ($this->having != '') {
                $sqlText .= ' HAVING ' . $this->having;
            }

            if (count($this->orderBy)) {
                $sqlText .= ' ORDER BY ' . implode($this->orderBy, ',');
            }
        }

        if ($this->forUpdate) {
            $sqlText .= ' FOR UPDATE';
        }

        if ($this->setOperation != NULL) {
            $sqlText .= $this->_getSetOperation();
        }

        if ($this->range) {
            preg_match_all('/select/i', $sqlText, $matches);
            if (count($matches[0]) > 1) { // set operation
                //$sqlText = 'SELECT * from (' . $sqlText . ') query ' . $this->db->getPlatform()->getSQLRange($this->range);
                $sqlText .= ' ' . $this->db->getPlatform()->getSQLRange($this->range);
            } else {
                $sqlText .= ' ' . $this->db->getPlatform()->getSQLRange($this->range);
            }
        }

        $this->command = $sqlText;

        $this->prepare();
        $this->bind();
        return $this;
    }

    public function execute($parameters = null)
    {
        $this->setParameters($parameters);
        $this->prepare();
        $this->bind();
        return $this->stmt->execute();
    }

    public function clear()
    {
        $this->join = null;
        $this->columns = [];
        $this->tables = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->parameters = [];
        $this->command = '';
        $this->range = null;
        $this->db = null;
        $this->stmt = null;
    }

    public function setParameters()
    {
        $numargs = func_num_args();
        if ($numargs > 0) {
            if ($numargs == 1) {
                $parameters = func_get_arg(0);
                if ($parameters === null) {
                    return;
                } elseif (is_object($parameters)) {
                    $object = $parameters;
                    $parameters = array();
                    foreach ($object as $attr => $value) {
                        $parameters[$attr] = $value;
                    }
                } elseif (!is_array($parameters)) {
                    $parameters = array($parameters);
                }
            } else {
                $parameters = func_get_args();
            }
            $this->parameters = $parameters;
        }
    }

    public function addParameter($value, $type = null)
    {
        if (is_array($value)) {
            $this->parameters[] = $value[0];
            $this->paramType[] = $value[1];
        } else {
            $this->parameters[] = $value;
            $this->paramType[] = $type;
        }
    }

    public function setRange()
    {
        $numargs = func_num_args();
        if ($numargs == 1) {
            $this->range = func_get_arg(0);
        } elseif ($numargs == 2) {
            $page = func_get_arg(0);
            $rows = func_get_arg(1);
            $this->range = new \MRange($page, $rows);
        }
    }

    public function setOffset($page, $rows)
    {
        if (!$this->range) {
            $this->range = new \MRange($page, $rows);
        }
    }

    private function _findStr($target, $source)
    {
        $l = strlen($target);
        $lsource = strlen($source);
        $pos = 0;

        while (($pos < $lsource) && (!$fim)) {
            if ($source[$pos] == "(") {
                $p = $this->findStr(")", substr($source, $pos + 1));

                if ($p > 0)
                    $pos += $p + 3;
            }

            $fim = ($target == substr($source, $pos, $l));

            if (!$fim)
                $pos++;
        }

        return ($fim ? $pos : -1);
    }

    public function parseSqlCommand(&$cmd, $clause, $delimiters)
    {
        if (substr($cmd, 0, strlen($clause)) != $clause)
            return false;

        $cmd = substr($cmd, strlen($clause));
        $n = count($delimiters);
        $i = 0;
        $pos = -1;

        while (($pos < 0) && ($i < $n))
            $pos = $this->_findStr($delimiters[$i++], $cmd);

        if ($pos > 0) {
            $r = substr($cmd, 0, $pos);
            $cmd = substr($cmd, $pos);
        }

        return $r;
    }

    public function createFrom($sqltext)
    {
        $this->command = $sqltext;
        $sqltext = trim($sqltext) . " #";
        $sqltext = preg_replace("/(?i)select /", "select ", $sqltext);
        $sqltext = preg_replace("/(?i) from /", " from ", $sqltext);
        $sqltext = preg_replace("/(?i) where /", " where ", $sqltext);
        $sqltext = preg_replace("/(?i) order by /", " order by ", $sqltext);
        $sqltext = preg_replace("/(?i) group by /", " group by ", $sqltext);
        $sqltext = preg_replace("/(?i) having /", " having ", $sqltext);
        $this->setColumns($this->parseSqlCommand($sqltext, "select", array("from")));

        if ($this->_findStr('JOIN', $sqltext) < 0) {
            $this->setTables($this->parseSqlCommand($sqltext, "from", array("where", "group by", "order by", "#")));
        } else {
            $this->join = $this->parseSqlCommand($sqltext, "from", array("where", "group by", "order by", "#"));
        }

        $this->setWhere($this->parseSqlCommand($sqltext, "where", array("group by", "order by", "#")));
        $this->setGroupBy($this->parseSqlCommand($sqltext, "group by", array("having", "order by", "#")));
        $this->setHaving($this->parseSqlCommand($sqltext, "having", array("order by", "#")));
        $this->setOrderBy($this->parseSqlCommand($sqltext, "order by", array("#")));
    }

}

