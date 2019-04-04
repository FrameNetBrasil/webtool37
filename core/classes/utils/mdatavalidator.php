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

class MDataValidator
{

    /**
     * @var array $validators an array of validator objects
     */
    private $validators = array();
    private $toValidate = array();

    public function __construct($toValidate = array())
    {
        $this->toValidate = $toValidate;
    }

    /**
     * Get a validator instance for the passed $name
     *
     * @param  string $name Name of the validator or the validator class name
     * @return Doctrine_Validator_Interface $validator
     */
    public function getValidator($name)
    {
        if (!isset($this->validators[$name])) {
            $class = 'Doctrine_Validator_' . ucwords(strtolower($name));
            if (class_exists($class)) {
                $this->validators[$name] = new $class;
            } else if (class_exists($name)) {
                $this->validators[$name] = new $name;
            } else {
                throw new Exception("Validator named '$name' not available.");
            }
        }
        return $this->validators[$name];
    }

    public function validate($object, $exception = true)
    {
        $errors = '';
        $result = true;
        foreach ($this->toValidate as $name => $constraints) {
            $value = $object->$name;
            foreach ($constraints as $validator => $args) {
                if ($validator == 'type') {
                    $ok = $this->isValidType($value, $args);
                } else {
                    $v = $this->getValidator($validator);
                    $v->args = $args;
                    $ok = $v->validate($value);
                }
                if (!$ok) {
                    $errors .= "[{$name}:{$validator} ({$value})]";
                }
                $result &= $ok;
            }
        }
        if (!$result) {
            if ($exception) {
                throw new Exception("Validation failed: {$errors}.");
            } else {
                return false;
            }
        }
        return true;
    }

    public function validateModel($object, $exception = true)
    {
        $attributes = $object->getAttributesMap();
        $errors = '';
        $result = true;
        foreach ($attributes as $name => $definitions) {
            $method = 'get' . $name;
            $value = $object->$method();
            // first, validate type
            $type = $definitions['type'];
            $ok = $this->isValidType($value, $type);
            if (!$ok) {
                $errors .= "[{$name}:$type ({$value})]";
            }
            $result &= $ok;
            // now, validate constraints
            $config = $object->config();
            $validators = $config['validators'][$name];
            if (is_array($validators)) {
                foreach ($validators as $index => $args) {
                    if (is_numeric($index)) {
                        $validator = $args;
                        $args = true;
                    } else {
                        $validator = $index;
                    }
                    $v = $this->getValidator($validator);
                    $v->args = $args;
                    $ok = $v->validate($value);
                    if (!$ok) {
                        $fieldDescription = $config['fieldDescription'][$name] ?: $name;
                        $msg = (Manager::getMessage("{$validator}", [$fieldDescription, $args, $value]) ?: $validator);
                        $errors .= "<br>- $fieldDescription $msg";
                    }
                    $result &= $ok;
                }
            }
        }
        if (!$result) {
            if ($exception) {
                throw new EDataValidationException("Validação falhou: {$errors}.");
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Validates a given record and saves possible errors in Doctrine_Validator::$stack
     *
     * @param  Doctrine_Record $record
     * @return void
     */
    public function validateRecord(Doctrine_Record $record)
    {
        $table = $record->getTable();

        // if record is transient all fields will be validated
        // if record is persistent only the modified fields will be validated
        $fields = $record->exists() ? $record->getModified() : $record->getData();
        foreach ($fields as $fieldName => $value) {
            $table->validateField($fieldName, $value, $record);
        }
        $table->validateUniques($record);
    }

    /**
     * Validates the length of a field.
     *
     * @param  string $value Value to validate
     * @param  string $type Type of field being validated
     * @param  string $maximumLength Maximum length allowed for the column
     * @return boolean $success       True/false for whether the value passed validation
     */
    public function validateLength($value, $type, $maximumLength)
    {
        if ($maximumLength === null) {
            return true;
        }
        if ($type == 'timestamp' || $type == 'integer' || $type == 'enum') {
            return true;
        } else if ($type == 'array' || $type == 'object') {
            $length = strlen(serialize($value));
        } else if ($type == 'decimal' || $type == 'float') {
            $value = abs($value);

            $localeInfo = localeconv();
            $decimalPoint = $localeInfo['mon_decimal_point'] ? $localeInfo['mon_decimal_point'] : $localeInfo['decimal_point'];
            $e = explode($decimalPoint, $value);

            $length = strlen($e[0]);

            if (isset($e[1])) {
                $length = $length + strlen($e[1]);
            }
        } else if ($type == 'blob') {
            $length = strlen($value);
        } else {
            $length = $this->getStringLength($value);
        }
        if ($length > $maximumLength) {
            return false;
        }
        return true;
    }

    /**
     * Get length of passed string. Will use multibyte character functions if they exist
     *
     * @param string $string
     * @return integer $length
     */
    public function getStringLength($string)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($string, 'utf8');
        } else {
            return strlen(utf8_decode($string));
        }
    }

    /**
     * Whether or not errors exist on this validator
     *
     * @return boolean True/false for whether or not this validate instance has error
     */
    public function hasErrors()
    {
        return (count($this->stack) > 0);
    }

    /**
     * Validate the type of the passed variable
     *
     * @param  mixed $var Variable to validate
     * @param  string $type Type of the variable expected
     * @return boolean
     */
    public function isValidType($var, $type)
    {
        if (($var === null) || ($var === '')) {
            return true;
        }

        switch ($type) {
            case 'float':
            case 'double':
            case 'decimal':
                return $this->validateNumeric($var);
            case 'integer':
                return ((string)$var) == strval(round(floatval($var)));
            case 'string':
            case 'text':
            case 'clob':
                return is_string($var) || is_numeric($var);
            case 'blob':
                return $var instanceof MFile;
            case 'gzip':
                return is_string($var);

            case 'array':
                return is_array($var);
            case 'collection':
                return $var instanceof MArray;
            case 'object':
                return is_object($var);
            case 'boolean':
                return is_bool($var) || (is_numeric($var) && ($var == 0 || $var == 1));
            case 'timestamp':
                return $var instanceof MTimestamp;
            case 'time':
                $validator = $this->getValidator('time');
                return $validator->validate($var);
            case 'date':
                return $var instanceof MDate;
            case 'currency':
                return $var instanceof MCurrency;
            case 'enum':
                return is_string($var) || is_int($var);
            case 'set':
                return is_array($var) || is_string($var);
            default:
                return true;
        }


    }

    private function validateNumeric($var)
    {
        $LocaleInfo = localeconv();
        $var = str_replace($LocaleInfo["mon_decimal_point"], ".", $var);
        return is_numeric($var);
    }

}
