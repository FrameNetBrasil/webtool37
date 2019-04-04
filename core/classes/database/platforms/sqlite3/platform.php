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

namespace database\platforms\Sqlite3;

class Platform extends \Doctrine\DBAL\Platforms\SqlitePlatform
{

    public $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function connect()
    {

    }

    public function getTypedAttributes()
    {
        return ''; //'lob,blob,clob,text';
    }

    public function getSetOperation($operation)
    {
        $operation = strtoupper($operation);
        $set = array('UNION' => 'UNION', 'INTERSECT' => 'INTERSECT', 'MINUS' => 'EXCEPT');
        return $set[$operation];
    }

    public function getNewId($sequence = '', $tableGenerator = 'manager_sequence')
    {
        return $this->getNextValue($sequence);
    }

    public function getNextValue($sequence = '', $tableGenerator = 'manager_sequence')
    {
        $sql = new \database\MSQL("value", $tableGenerator, "(lower(sequence)='" . strtolower($sequence) . "')");
        $sql->setDb($this->db);
        $result = $this->db->query($sql->select());
        $value = $result[0][0];
        $sql = new \database\MSQL("value", $tableGenerator, "(lower(sequence)='" . strtolower($sequence) . "')");
        $sql->setDb($this->db);
        $result = $this->db->query($sql->update($value + 1));
        return $value;
    }

    public function getMetaData($stmt)
    {
        $s = $stmt->getWrappedStatement()->getWrappedResult();
        $metadata['columnCount'] = $count = $s->numColumns();
        for ($i = 0; $i < $count; $i++) {
            $name = strtoupper($s->columnName($i));
            $metadata['fieldname'][$i] = $name;
            $metadata['fieldtype'][$name] = $this->_getMetaType($s->columnType($i));
            $metadata['fieldlength'][$name] = 0;
            $metadata['fieldpos'][$name] = $i;
        }
        return $metadata;
    }

    private function _getMetaType($sqlite3_type)
    {
        if ($pdo_type == \SQLITE3_NULL) {
            $type = ' ';
        } else if ($pdo_type == \SQLITE3_INTEGER) {
            $type = 'N';
        } else if ($pdo_type == \SQLITE3_TEXT) {
            $type = 'C';
        } else if ($pdo_type == \SQLITE3_BLOB) {
            $type = 'O';
        } else if ($pdo_type == \SQLITE3_FLOAT) {
            $type = 'O';
        } else {
            $type = 'C';
        }
        return $type;
    }

    public function getSQLRange(\MRange $range)
    {
        return " LIMIT " . $range->rows . " OFFSET " . $range->offset;
    }

    public function fetchAll($query)
    {
        return $query->msql->stmt->fetchAll($query->fetchStyle);
    }

    public function fetchObject($query)
    {
        $stmt = $query->msql->stmt->getWrappedStatement();
        return $stmt->fetchObject();
    }

    public function convertToDatabaseValue($value, $type, &$bindingType)
    {
        if ($value === NULL) {
            return $value;
        }
        if ($type == '') {
            if (is_object($value)) {
                $type = substr(strtolower(get_class($value)), 1);
            }
        }
        if ($type == 'date') {
            return $value->format('Y-m-d');
        } elseif ($type == 'timestamp') {
            return $value->format('Y-m-d H:i:s');
        } elseif (($type == 'decimal') || ($type == 'float')) {
            return (float)str_replace(',', '.', $value);
        } elseif ($type == 'currency') {
            return $value->getValue();
        } elseif ($type == 'cpf') {
            return $value->getPlainValue();
        } elseif ($type == 'cnpj') {
            return $value->getPlainValue();
        } elseif ($type == 'boolean') {
            return (empty($value) ? '0' : '1');
        } elseif ($type == 'blob') {
            return base64_encode($value->getValue());
        } else {
            return $value;
        }
    }

    public function convertToPHPValue($value, $type)
    {
        if ($type == 'date') {
            return \Manager::Date($value);
        } elseif ($type == 'timestamp') {
            return \Manager::Timestamp($value);
        } elseif ($type == 'currency') {
            return \Manager::currency($value);
        } elseif ($type == 'cnpj') {
            return \MCNPJ::create($value);
        } elseif ($type == 'cpf') {
            return \MCPF::create($value);
        } elseif ($type == 'currency') {
            return \Manager::currency($value);
        } elseif ($type == 'boolean') {
            return (!empty($value));
        } elseif ($type == 'blob') {
            $value = \MFile::file(base64_decode($value));
            return $value;
        } else {
            return $value;
        }
    }

    public function convertColumn($value, $type)
    {
        if ($type == 'date') {
            return "strftime('" . $this->db->getConfig('formatDate') . "'," . $value . ") ";
        } elseif ($type == 'timestamp') {
            return "strftime('" . $this->db->getConfig('formatDate') . ' ' . $this->db->getConfig('formatTime') . "'," . $value . ") ";
        } else {
            return $value;
        }
    }

    public function convertWhere($value, $type = '')
    {
        if ($type == '') {
            if (is_object($value)) {
                $type = substr(strtolower(get_class($value)), 1);
            }
        }
        return $value;
    }

    public function handleTypedAttribute($attributeMap, $operation, $object)
    {
        /*
          $method = 'handle' . $attributeMap->getType();
          $this->$method($attributeMap, $operation, $object);
         *
         */
    }

    public function setUserInformation($userId, $userIP = null, $module = null, $action = null)
    {

    }

}
