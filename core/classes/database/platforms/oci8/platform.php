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

namespace database\platforms\OCI8;

use database\MDatabase;

class Platform extends \Doctrine\DBAL\Platforms\OraclePlatform
{

    public $db;
    public $executeMode = OCI_COMMIT_ON_SUCCESS;
    private $linguistic = false;

    public function __construct(MDatabase $db)
    {
        $this->db = $db;
    }

    public function connect()
    {
        $nlsLang = $this->db->getConfig('nls_lang');
        if ($nlsLang) {
            putenv("NLS_LANG=" . $nlsLang);
        }
        $nlsDate = 'YYYY/MM/DD';
        $nlsTime = $this->db->getConfig('formatTime');
        putenv("NLS_DATE_FORMAT=" . $nlsDate . ' ' . $nlsTime);
        $this->db->getConnection()->exec("alter session set NLS_DATE_FORMAT='" . $nlsDate . ' ' . $nlsTime . "'");
        $this->db->getConnection()->exec("alter session set NLS_TIMESTAMP_FORMAT='" . $nlsDate . ' ' . $nlsTime . "'");
    }

    public function getTypedAttributes()
    {
        return 'blob'; //'lob,blob,clob,text';
    }

    public function getSetOperation($operation)
    {
        $operation = strtoupper($operation);
        $set = array('UNION' => 'UNION', 'UNION ALL' => 'UNION ALL', 'INTERSECT' => 'INTERSECT', 'MINUS' => 'MINUS');
        return $set[$operation];
    }

    public function getNewId($sequence = 'admin', $tableGenerator = 'cm_sequence')
    {
        try {
            $this->value = $this->getNextValue($sequence);
            return $this->value;
        } catch (\Exception $ex) {
            throw $ex;
        }

    }

    public function getNextValue($sequence = 'admin', $tableGenerator = 'cm_sequence')
    {
        try {
            $sql = new \database\MSQL($sequence . '.nextval as value', 'dual');
            $result = $this->db->query($sql);
            $value = $result[0][0];
            return $value;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getMetadata($stmt)
    {
        $s = $stmt->getWrappedStatement();
        $metadata['columnCount'] = $count = $s->columnCount();
        for ($i = 0; $i < $count; $i++) {
            $meta = $this->_getColumnMeta($s->getHandle(), $i);
            $name = strtoupper($meta['name']);
            $metadata['fieldname'][$i] = $name;
            $metadata['fieldtype'][$name] = $meta['type'];
            $metadata['fieldlength'][$name] = $meta['len'];
            $metadata['fieldpos'][$name] = $i;
        }
        return $metadata;
    }

    private function _getColumnMeta($stmt, $columnIndex = 0)
    {
        $meta['name'] = \strtoupper((oci_field_name($stmt, $columnIndex + 1)));
        $meta['len'] = oci_field_size($stmt, $columnIndex + 1);
        $type = oci_field_type($stmt, $columnIndex + 1);
        $rType = 'C';
        if ($type == "VARCHAR") {
            $rType = 'C';
        } elseif ($type == "CHAR") {
            $rType = 'C';
        } elseif ($type == "NUMBER") {
            $rType = 'N';
        } elseif ($type == "DATE") {
            $rType = 'D';
        } elseif ($type == "TIMESTAMP") {
            $rType = 'D';
        } elseif ($type == "BLOB") {
            $rType = 'O';
        } elseif ($type == "CLOB") {
            $rType = 'O';
        }
        $meta['type'] = $rType;
        return $meta;
    }

    public function getSQLRange(\MRange $range)
    {
        return "";
    }

    public function fetchAll($query)
    {
        $offset = $query->msql->range ? $query->msql->range->offset : 0;
        $maxrows = $query->msql->range ? $query->msql->range->rows : -1;

        $fetchStyle = $this->convertPdoToOciFetchStyle($query->fetchStyle) +
            OCI_FETCHSTATEMENT_BY_ROW + OCI_RETURN_LOBS;

        $stmt = $query->msql->stmt->getWrappedStatement()->getHandle();


        $rowCount = oci_fetch_all($stmt, $result, $offset, $maxrows, $fetchStyle);


        if ($rowCount === false) {
            throw new EDatabaseQueryException(oci_error($stmt));
        }
        return $result;
    }

    /**
     * Converte as constantes usadas no tipo de fetch para as do OCI.
     * @param $fetchStyle
     * @return int
     */
    private function convertPdoToOciFetchStyle($fetchStyle)
    {
        switch ($fetchStyle) {
            case \PDO::FETCH_ASSOC:
                return OCI_ASSOC;
            case \PDO::FETCH_NUM:
                return OCI_NUM;
            case \PDO::FETCH_BOTH:
                return OCI_BOTH;
            default:
                return $fetchStyle;
        }
    }

    public function fetchObject($query)
    {
        $stmt = $query->msql->stmt->getWrappedStatement()->getHandle();
        return oci_fetch_object($stmt);
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

        switch ($type) {
            case 'blob':
                $bindingType = \PDO::PARAM_LOB;
                return "EMPTY_BLOB()";
            case 'date':
                return $value->format('Y/m/d');
            case 'timestamp':
                return $value->format('Y/m/d H:i:s');
            case 'currency':
                return $value->getValue();
            case 'decimal':
            case 'float':
                return str_replace('.', ',', $value);
            case 'boolean':
                return (empty($value) ? '0' : '1');
            case 'cpf':
            case 'cnpj':
                return $value->getPlainValue();
            case 'collection':
                $arr = $value->getValue();
                return json_encode($arr);
            default:
                return $value;
        }
    }

    public function convertToPHPValue($value, $type)
    {
        switch ($type) {
            case 'currency':
                return \Manager::currency($value);
            case 'decimal':
            case 'float':
                return str_replace(',', '.', $value);
            case 'cpf':
                return \MCPF::create($value);
            case 'cnpj':
                return \MCNPJ::create($value);
            case 'collection':
                $arr = json_decode($value, true);
                return new \MArray($arr);
            case 'clob':
                return $this->parseClob($value);
            case 'blob':
                return $this->parseBlob($value);
            case 'boolean':
            case 'date':
            case 'timestamp':
            default:
                return $value;
        }
    }

    private function parseBlob($value)
    {
        $parsedValue = '';
        if (is_resource($value) or is_resource($value->descriptor)) {
            $value->rewind();
            while (!$value->eof()) {
                $parsedValue .= $value->read(2000);
            }
            $value = \MFile::file($parsedValue);
        } else {
            $value = \MFile::file($value);
        }

        return $value;
    }

    private function parseClob($value)
    {
        return is_a($value, '\OCI-Lob') ? $value->load() : $value;
    }

    public function convertColumn($value, $dbalType)
    {
        if ($dbalType == 'date') {
            return "TO_CHAR(" . $value . ",'" . $this->db->getConfig('formatDate') . "') ";
        } elseif ($dbalType == 'timestamp') {
            return "TO_CHAR(" . $value . ",'" . $this->db->getConfig('formatDate') . ' ' . $this->db->getConfig('formatTime') . "') ";
        } else {
            return $value;
        }
    }

    public function convertWhere($value, $type)
    {
        if ($type == '') {
            if (is_object($value)) {
                $type = substr(strtolower(get_class($value)), 1);
            }
        }
        if ($type == 'date') {
            return "TO_DATE('" . $value->format('Y-m-d') . "','YYYY-MM-DD') ";
        } elseif ($type == 'timestamp') {
            return "TO_DATE('" . $value->format('Y-m-d H:i:s') . "','YYYY-MM-DD HH24:MI:SS') ";
        } else {
            return $value;
        }
    }

    public function handleTypedAttribute($attributeMap, $operation, $object)
    {
        $method = 'handle' . $attributeMap->getType();
        $this->$method($attributeMap, $operation, $object);
    }

    /**
     * Permite que o Oracle mantenha informações sobre o usuário da aplicação e a vincule com a sessão específica.
     * @param type $userId Identificador do usuário da aplicação
     * @param type $userIp Ip da máquina do usuário
     * @param type $module Classe, Script ou módulo de onde está partindo a execução.
     * @param type $action Ação executada pelo usuário.
     */
    public function setUserInformation($userId, $userIP, $module, $action)
    {
        $this->db->getConnection()
            ->getWrappedConnection()
            ->setUserInformation($userId, $userIP, $module, $action);
    }

    private function handleBLOB($attributeMap, $operation, $object)
    {
        //mdump('platform::handleBLOB');
        $classMap = $attributeMap->getClassMap();
        $statement = $classMap->getSelectStatement();
        $pkName = $classMap->getKeyAttributeName();
        $statement->addParameter($object->get($pkName));
        $statement->setForUpdate(true);
        $query = $this->db->getQuery($statement);
        $file = $attributeMap->getValue($object);
        $value = $file ? $file->getValue() : '';
        $column = $attributeMap->getColumnName();
        if (($operation == 'insert') || ($operation == 'update')) {
            $row = $query->fetchObject();
            $column = strtoupper($column);
            if (isset($row->$column)) {
                $row->$column->truncate();
                $row->$column->save($value);
                $row->$column->free();
                $logger = $this->db->getConnection()->getConfiguration()->getSQLLogger();
                $logger->startQuery('BLOB ' . $operation . ' - column ' . $column);
            }
        }
        if (($operation == 'select')) { // handled directly by oci_fetch_all (query)
        }
    }

    public function ignoreAccentuation($ignore = true)
    {
        if ($ignore) {
            $this->enableLinguisticSearchs();
        } else {
            $this->disableLinguisticSearchs();
        }
    }

    private function enableLinguisticSearchs()
    {
        if (!$this->linguistic) {
            $trans = $this->db->beginTransaction();
            $this->db->executeCommand('ALTER SESSION SET NLS_COMP=LINGUISTIC');
            $this->db->executeCommand('ALTER SESSION SET NLS_SORT=BINARY_AI');
            $trans->commit();
            $this->linguistic = true;
        }
    }

    private function disableLinguisticSearchs()
    {
        if ($this->linguistic) {
            $trans = $this->db->beginTransaction();
            $this->db->executeCommand('ALTER SESSION SET NLS_COMP=BINARY');
            $this->db->executeCommand('ALTER SESSION SET NLS_SORT=WEST_EUROPEAN');
            $trans->commit();
            $this->linguistic = false;
        }
    }
}

function handleText($attributeMap, $operation)
{
    //mdump('platform::handleText');
}
