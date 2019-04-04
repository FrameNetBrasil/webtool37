<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\DBAL\Driver\SQLite3;

use PDO;
use IteratorAggregate;
use Doctrine\DBAL\Driver\Statement;

/**
 * The SQLite3 implementation of the Statement interface.
 *
 * @since 1.1
 * @author Ely Matos <ely.matos@ufjf.edu.br>
 */
class SQLite3Statement implements \IteratorAggregate, Statement {

    /** Statement handle. */
    protected $_dbh;
    protected $_sth;
    protected $_conn;
    protected $_ret;
    protected static $_PARAM = ':param';
    protected static $fetchModeMap = array(
        PDO::FETCH_BOTH => SQLITE3_BOTH,
        PDO::FETCH_ASSOC => SQLITE3_ASSOC,
        PDO::FETCH_NUM => SQLITE3_NUM,
    );
    protected $_defaultFetchMode = PDO::FETCH_BOTH;
    protected $_paramMap = array();

    /**
     * Creates a new OCI8Statement that uses the given connection handle and SQL statement.
     *
     * @param resource $dbh The connection handle.
     * @param string $statement The SQL statement.
     */
    public function __construct($dbh, $statement, SQLite3Connection $conn) {
        list($statement, $paramMap) = self::convertPositionalToNamedPlaceholders($statement);
        $this->_sth = $dbh->prepare($statement);
        $this->_dbh = $dbh;
        $this->_ret = null;
        $this->_paramMap = $paramMap;
        $this->_conn = $conn;
    }

    /**
     * Convert positional (?) into named placeholders (:param<num>)
     *
     * Oracle does not support positional parameters, hence this method converts all
     * positional parameters into artificially named parameters. Note that this conversion
     * is not perfect. All question marks (?) in the original statement are treated as
     * placeholders and converted to a named parameter.
     *
     * The algorithm uses a state machine with two possible states: InLiteral and NotInLiteral.
     * Question marks inside literal strings are therefore handled correctly by this method.
     * This comes at a cost, the whole sql statement has to be looped over.
     *
     * @todo extract into utility class in Doctrine\DBAL\Util namespace
     * @todo review and test for lost spaces. we experienced missing spaces with oci8 in some sql statements.
     * @param string $statement The SQL statement to convert.
     * @return string
     */
    static public function convertPositionalToNamedPlaceholders($statement) {
        $count = 1;
        $inLiteral = false; // a valid query never starts with quotes
        $stmtLen = strlen($statement);
        $paramMap = array();
        for ($i = 0; $i < $stmtLen; $i++) {
            if ($statement[$i] == '?' && !$inLiteral) {
                // real positional parameter detected
                $paramMap[$count] = ":param$count";
                $len = strlen($paramMap[$count]);
                $statement = substr_replace($statement, ":param$count", $i, 1);
                $i += $len - 1; // jump ahead
                $stmtLen = strlen($statement); // adjust statement length
                ++$count;
            } else if ($statement[$i] == "'" || $statement[$i] == '"') {
                $inLiteral = !$inLiteral; // switch state!
            }
        }

        return array($statement, $paramMap);
    }

    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = null) {
        return $this->bindParam($param, $value, $type, null);
    }

    /**
     * {@inheritdoc}
     */
    public function bindParam($column, &$variable, $type = null, $length = null) {
        if ($type === null) {
            $type = SQLITE3_TEXT;
        }
        $column = isset($this->_paramMap[$column]) ? $this->_paramMap[$column] : $column;
        return $this->_sth->bindParam($column, $variable, $type);
    }

    /**
     * Closes the cursor, enabling the statement to be executed again.
     *
     * @return boolean              Returns TRUE on success or FALSE on failure.
     */
    public function closeCursor() {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function columnCount() {
        return $this->_result->numColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode() {
        $code = $this->_dbh->lastErrorCode(); 
        return (($code == 0)  || ($code == 101) ? 0 : $code);
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo() {
        return array('message' => $this->_dbh->lastErrorMsg(), 'code' => $this->errorCode());
    }

    /**
     * {@inheritdoc}
     */
    public function execute($params = null) {
        if ($params) {
            $hasZeroIndex = array_key_exists(0, $params);
            foreach ($params as $key => $val) {
                if ($hasZeroIndex && is_numeric($key)) {
                    $this->bindValue($key + 1, $val);
                } else {
                    $this->bindValue($key, $val);
                }
            }
        }

        $this->_ret = $this->_sth->execute();
        if (!$this->_ret) {
            throw SQLite3Exception::fromErrorInfo($this->errorInfo());
        }
        return $this->_ret;
    }

    /**
     * {@inheritdoc}
     */
    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null) {
        $this->_defaultFetchMode = $fetchMode;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator() {
        $data = $this->fetchAll();
        return new \ArrayIterator($data);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($fetchMode = null) {
        $fetchMode = $fetchMode ? : $this->_defaultFetchMode;
        if (!isset(self::$fetchModeMap[$fetchMode])) {
            throw new \InvalidArgumentException("Invalid fetch style: " . $fetchMode);
        }
        return $this->_ret->fetchArray(self::$fetchModeMap[$fetchMode]);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchMode = null) {
        $fetchMode = $fetchMode ? : $this->_defaultFetchMode;
        if (!isset(self::$fetchModeMap[$fetchMode])) {
            throw new \InvalidArgumentException("Invalid fetch style: " . $fetchMode);
        }

        $result = array();
        while ($row = $this->fetch($fetchMode)) {
            $result[] = $row;
        }
        $this->_result = $result;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($columnIndex = 0) {
        $row = $this->_ret->fetchArray(SQLITE3_NUM);
        return isset($row[$columnIndex]) ? $row[$columnIndex] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchObject() {
        $row = $this->_ret->fetchArray(SQLITE3_ASSOC);
        $object = ($row) ? (object) $row : false;
        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function rowCount() {
        return count($this->_result);
    }

    public function getHandle() {
        return $this->_sth;
    }

    /**
     * Gets the wrapped statement result.
     *
     * @return SQLite3Result
     */
    public function getWrappedResult() {
        return $this->_ret;
    }

}
