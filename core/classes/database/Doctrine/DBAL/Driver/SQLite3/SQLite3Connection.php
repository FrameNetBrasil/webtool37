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

use Doctrine\DBAL\Platforms\SQLitePlatform;

/**
 * SQLite3 implementation of the Connection interface.
 *
 * @since 1.1
 */
class SQLite3Connection implements \Doctrine\DBAL\Driver\Connection
{
    /**
     * @var resource
     */
    protected $dbh;

    /**
     * @var int
     */
    protected $executeMode = OCI_COMMIT_ON_SUCCESS;

    /**
     * Create a Connection to an Oracle Database using oci8 extension.
     *
     * @param string $username
     * @param string $password
     * @param string $db
     */
    public function __construct($db, $charset = null)
    {
        $this->dbh = new \SQLite3($db);

        if ( ! $this->dbh) {
            throw SQLite3Exception::fromErrorInfo(array('message' => $this->dbh->lastErrorMsg(), 'code' => $this->dbh->lastErrorCode()));
        }
    }

    /**
     * Create a non-executed prepared statement.
     *
     * @param  string $prepareString
     * @return OCI8Statement
     */
    public function prepare($prepareString)
    {
        return new SQLite3Statement($this->dbh, $prepareString, $this);
    }

    /**
     * @param string $sql
     * @return OCI8Statement
     */
    public function query()
    {
        $args = func_get_args();
        $sql = $args[0];
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Quote input value.
     *
     * @param mixed $input
     * @param int $type PDO::PARAM*
     * @return mixed
     */
    public function quote($value, $type=\PDO::PARAM_STR)
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        $value = str_replace("'", "''", $value);
        return "'" . addcslashes($value, "\000\n\r\\\032") . "'";
    }

    /**
     *
     * @param  string $statement
     * @return int
     */
    public function exec($statement)
    {
        $stmt = $this->prepare($statement);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId($name = null)
    {
        return (int) $this->dbh->lastInsertRowId;
    }

    /**
     * Start a transactiom
     *
     * Oracle has to explicitly set the autocommit mode off. That means
     * after connection, a commit or rollback there is always automatically
     * opened a new transaction.
     *
     * @return bool
     */
    public function beginTransaction()
    {
        $this->dbh->exec('BEGIN TRANSACTION');
        return true;
    }

    /**
     * @throws OCI8Exception
     * @return bool
     */
    public function commit()
    {
        $this->dbh->exec('COMMIT');
        return true;
    }

    /**
     * @throws OCI8Exception
     * @return bool
     */
    public function rollBack()
    {
        $this->dbh->exec('ROLLBACK');
        return true;
    }

    public function errorCode()
    {
        $code = $this->dbh->lastErrorCode(); 
        return (($code == 0)  || ($code == 101) ? 0 : $code);
    }

    public function errorInfo()
    {
        return $this->dbh->lastErrorMsg();
    }
}
