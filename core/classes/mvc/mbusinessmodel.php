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

/**
 * Classe base de todos os Business Models.
 * Business Models são modelos que contém regras de negócio e são, geralmente, persistentes.
 *
 * @category    Maestro
 * @package     Core
 * @subpackage  MVC
 * @version     1.0
 * @since       1.0
 */
use ProxyManager\Factory\AccessInterceptorValueHolderFactory as Factory;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\Proxy\GhostObjectInterface;

class MBusinessModel extends PersistentObject
    implements JsonSerializable, Serializable
{

    /**
     * Namespace do Model.
     * @var string
     */
    private $_namespace;

    /**
     * ORMMap do Model.
     * @var array
     */
    private $_map;

    /**
     * Instancia Model e opcionalmente inicializa atributos com $data.
     * @param mixed $data
     */

    /**
     * Dados do Model originais do registro. setData nao influencia o originalData
     * @param mixed $originalData
     */
    private $_originalData;
    protected $_baseProxyModel;
    protected $_proxyModel;
    protected $_initialized = [];

    public function __construct($data = NULL, $model = null)
    {
        parent::__construct();
        $this->_proxyModel = $model;
        if (is_callable(array($this, 'ORMMap'))) {
            $this->_map = $this->ORMMap();
        } else {
            $this->_map = $this->_proxyModel->ORMMap();
        }
        //$this->_mapClassName = ($this->_proxyModel != null) ? $this->_className : get_parent_class($this);
        $this->_className = ($this->_proxyModel != null) ? get_class($this->_proxyModel) : get_class($this);
        $this->_mapClassName = ($this->_proxyModel != null) ? get_class($this->_proxyModel) : get_parent_class($this);
        $p = strrpos($this->_className, '\\');
        $this->_namespace = substr($this->_className, 0, $p);
        $this->onCreate($data);
    }

    public function __call($name, $arguments)
    {

        //mdump('Calling on MBusinessModel: ' . $name);
        //mdump('proxyclass = ' . get_class($this->_proxyModel));
        if (is_callable(array($this->_proxyModel, $name))) {
            //if (method_exists($this->_proxyModel, $name)) {
            return $this->_proxyModel->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3], null);
        }
        throw new \BadMethodCallException("Method [{$name}] doesn't exists in " . get_class($this) . " class.");
    }

    /*
    public function __call($name, $arguments) {
        
        mdump('Calling ' . $name);
        //mdump('proxyclass = ' . get_class($this->_proxyModel));
        if (is_callable(array($this->_proxyModel, $name))) {
            return $this->_proxyModel->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3],null);
        }
        //mdump('baseProxyclass = ' . get_class($this->_baseProxyModel));
        //if (is_callable(array($this->_baseProxyModel, $name))) {
        //    return $this->_baseProxyModel->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
        //}
        throw new \BadMethodCallException("Method [{$name}] doesn't exists in " . get_class($this) . " class.");
    }

    public function defineProxyModel($model) {
        $baseModel = null;
        if ($model != null) {
            $baseModel = $model;
        } else {
            if (strpos($this->_className, 'maestro')) {
                $function = new \ReflectionClass($this->_className);
                $modelName = strtolower($function->getShortName());
                $modelClass = str_replace("persistence\\maestro\\{$modelName}", 'models', $this->_className);
                $baseModel = new $modelClass;
            }
        }
        $this->_baseProxyModel = $proxy = $baseModel;
        if ($baseModel != null) {
            $that = $this;
            $factory     = new LazyLoadingGhostFactory();
            $lowerMap = [];
            foreach ($this->_map['associations'] as $property => $association) {
                $lowerMap[strtolower($property)] = $property;
            }
            //mdump($lMap);
            $initializer = function (
                GhostObjectInterface $ghostObject,
                $method,
                $parameters,
                & $initializer
                //& $properties
            ) use ($that, $lowerMap) {
                //$initializer = null; // disable initialization
                mdump('initializer method = ' . $method);
                //mdump(get_class($ghostObject));
                $lowerMethod = strtolower($method);
                if (substr($lowerMethod, 0, 3) == 'get') {
                    $property = substr($lowerMethod,3);
                    if ($lowerMap[$property]) {
                        $setMethod = 'set' . $property;
                        $ghostObject->$setMethod($that->$method());
                        return false;
                    }
                    return false; // confirm that initialization occurred correctly
                }
                return true;
            };
            $proxy = $factory->createProxy(get_class($baseModel), $initializer);
        }

        $this->_proxyModel = $proxy;
    }
*/
    /*
    public function createProxyModel($model) {
        if ($model == null) {
            return;
        }
        $factory = new Factory();
        $that = $this;
        $prefixInterceptors = [];
        foreach ($this->_map['associations'] as $property => $association) {
            $method = 'get' . ucfirst($property);
            mdump($association);
            mdump('creating proxy ' . $property . '  method = ' . $method);
            $prefixInterceptors[$method] = function () use ($that, $method) {
                $that->$method();
            };
        }
        $proxy = $factory->createProxy($model, $prefixInterceptors, []);
        return $proxy;
    }


    public function getModel() {
        return $this->_proxyModel;
    }
*/
    public function getMap()
    {
        return $this->_map;
    }

    /**
     * Inicializa atributos com $data.
     * @param mixed $data
     * @return void
     */
    public function onCreate($data = NULL)
    {
        if (is_null($data)) {
            return;
        } elseif (is_object($data)) {
            $oid = $this->getOIDName();
            $id = $data->$oid ?: $data->id;
            $this->getById($id);
            $this->setOriginalData();
            $this->setData($data);
        } else {
            $this->getById($data);
            $this->setOriginalData();
        }
    }

    /**
     * Instancia Model e opcionalmente inicializa atributos com $data.
     * @param type $data
     * @return static
     */
    public static function create($data = NULL)
    {
        $className = get_called_class();
        return new $className($data);
    }

    /**
     * Array de configuração do Model.
     * @return array
     */
    public static function config()
    {
        return [
            'log' => [],
            'validators' => [],
            'converters' => []
        ];
    }

    /**
     * Nome da classe do Model.
     * @return string
     */
    public function getClassName()
    {
        return $this->_className;
    }

    /**
     * Namespace do Model.
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Array com o mapa de atributos do Model.
     * @return array
     */
    public function getAttributesMap()
    {
        $attributes = array();
        $map = $this->_map;
        do {
            $attributes = array_merge($attributes, $map['attributes']);
            if (!empty($map['extends'])) {
                $class = $map['extends'];
                $map = $class::ORMMap();
            } else {
                $map = null;
            }
        } while ($map);
        return $attributes;
    }

    /**
     * Array com o mapa de associações do Model.
     * @return array
     */
    public function getAssociationsMap()
    {
        return $this->_map['associations'];
    }

    /**
     * Valor do atributo de descrição do Model.
     * @return string
     */
    public function getDescription()
    {
        if ($this->_proxyModel) {
            if (method_exists($this->_proxyModel, 'getDescription')) {
                return $this->_proxyModel->getDescription();
            }
        }
        return $this->_className;
    }

    public function logIsEnabled()
    {
        $config = $this->config();
        return count($config['log']) > 0;
    }

    /**
     * Descrição usada para Log.
     * @return string
     */
    public function getLogDescription()
    {
        if (!$this->logIsEnabled()) {
            return '';
        }

        $config = $this->config();

        if ($config['log'][0] === true) {
            $data = $this->getDiffData();
        } else {
            $data = new stdClass();
            foreach ($config['log'] as $attr) {
                $data->$attr = (string)$this->get($attr);
            }
        }

        return json_encode($data, 10);
    }

    /**
     * Inicaliza atributos com base no OID.
     * @param type $id
     * @return \MBusinessModel
     */
    public function getById($id)
    {
        if (($id === '') || ($id === NULL)) {
            return;
        }

        $this->set($this->getPKName(), $id);

        $this->retrieve();
        return $this;
    }

    public function save($force = false) {
        if (!$this->isPersistent() || $this->wasChanged() || $force == true) {
            parent::save();
            $this->setOriginalData();
            return true;
        }
        return false;
    }

    public function delete()
    {
        parent::delete();
    }

    public static function getByIds(array $ids)
    {
        $instance = new static;

        return $instance->getCriteria()
            ->where($instance->getPKName(), 'in', $ids)
            ->asCursor()
            ->getObjects();
    }

    /**
     * Criteria genérico do Model. $filter indica filtros a serem usados na
     * consulta, $attribute indica os atributos a serem retornados e $order
     * o atributo usado para ordenar o resultado da consulta.
     * @param object $filter
     * @param string $attribute
     * @param string $order
     * @return criteria
     */
    public function listAll($filter = '', $attribute = '', $order = '')
    {
        $criteria = $this->getCriteria();
        if ($attribute != '') {
            $criteria->addCriteria($attribute, 'LIKE', "'{$filter}%'");
        }
        if ($order != '') {
            $criteria->addOrderAttribute($order);
        }
        return $criteria;
    }

    /**
     * Método auxiliar para montagem de grids de dados.
     * Retorna objeto JSON relativo a um criteria ou um array de dados. Os atributos "page" (número da página, 0-based)
     * e "rows" (número de linhas a serem retornadas) devem estar definidos em $this->data.
     * @param basecriteria|array $source Fonte de dados.
     * @param boolean $rowsOnly Se o JSON deve conter apenas os dados das linhas ou se deve conter também o total.
     * @param integer total
     * @return JSON object
     */
    public function gridDataAsJSON($source, $rowsOnly = false, $total = 0)
    {
        $data = Manager::getData();
        $result = (object) [
            'rows' => array(),
            'total' => 0
        ];
        if ($source instanceof BaseCriteria) {
            $criteria = $source;
            $result->total = $criteria->asQuery()->count();
            //if ($data->page > 0) {
            //    $criteria->range($data->page, $data->rows);
            //}
            $source = $criteria->asQuery();
        }
        if ($source instanceof database\mquery) {
            $result->total = $source->count();
            if ($data->page > 0) {
                $source->setRange($data->page, $data->rows);
            }
            $result->rows = $source->asObjectArray();
		} elseif (is_array($source)) {
            $rows = array();
            foreach ($source as $row) {
                $r = new \StdClass();
                foreach ($row as $c => $col) {
                    $field = is_numeric($c) ? 'F' . $c : $c;
                    $r->$field = "{$col}";
                }
                $rows[] = $r;
            }
            $result->rows = $rows;
            $result->total = ($total != 0) ? $total : count($rows);
        }
        if ($rowsOnly) {
            return MJSON::encode($result->rows);
        } else {
            return MJSON::encode($result);
        }
    }

    /**
     * Novo OID, usado em operações de inserção.
     * @param string $idGenerator
     * @return integer
     */
    public function getNewId($idGenerator)
    {
        return $this->getDb()->getNewId($idGenerator);
    }

    /**
     * Retorna handler para a conexão corrente no Database.
     * @return \Doctrine\DBAL\Connection
     */
    public function getTransaction()
    {
        return $this->getDb()->getTransaction();
    }

    /**
     * Coloca a conexão em estado de transação e retorna um handler para a
     * conexão.
     * @return \Doctrine\DBAL\Connection
     */
    public function beginTransaction()
    {
        return $this->getDb()->beginTransaction();
    }

    /**
     * Atribui $value para o atributo $attribute.
     * @param string $attribute
     * @param mixed $value
     */
    public function set($attribute, $value)
    {
        $method = 'set' . $attribute;
        $this->$method($value);
    }

    /**
     * Valor corrente do atributo $attribute.
     * @param string $attribute
     * @return mixed
     */
    public function get($attribute)
    {
        $method = 'get' . $attribute;
        return $this->$method();
    }

    /**
     * O objeto referenciado em associações oneToOne é definido com base em seu OID.
     * @param string $associationName
     * @param integer $id
     * @throws EPersistentManagerException
     */
    public function setAssociationId($associationName, $id)
    {
        $classMap = $this->getClassMap();
        $associationMap = $classMap->getAssociationMap($associationName);
        if (is_null($associationMap)) {
            throw new EPersistentManagerException("Association name [{$associationName}] not found.");
        }
        $fromAttribute = $associationMap->getFromAttributeMap()->getName();
        $toClass = $associationMap->getToClassName();
        if ($associationMap->getCardinality() == 'oneToOne') {
            $refObject = new $toClass($id);
            $this->set($associationName, $refObject);
            $this->set($fromAttribute, $id);
        } else {
            $array = array();
            if (!is_array($id)) {
                $id = array($id);
            }
            foreach ($id as $oid) {
                $array[] = new $toClass($oid);
            }
            $this->set($associationName, $array);
        }
    }

    /**
     * Retorna um ValueObject com atributos com valores planos (tipo simples).
     * @return \stdClass
     */
    public function getData()
    {
        $data = new stdClass();
        $attributes = $this->getAttributesMap();
        foreach ($attributes as $attribute => $definition) {
            $method = 'get' . $attribute;
            if (method_exists($this, $method)) {
                $rawValue = $this->$method();
            } else if (method_exists($this->_proxyModel, $method)) {
                $rawValue = $this->_proxyModel->$method();
            }
            $type = $definition['type'];
            if (isset($rawValue)) {
                $conversion = 'getPlain' . $type;
                $value = MTypes::$conversion($rawValue);
                $data->$attribute = $value;
                if (isset($definition['key']) && ($definition['key'] == 'primary')) {
                    $data->id = $value;
                    $data->idName = $attribute;
                }
            }
        }
        $data->description = $this->getDescription();
        return $data;
    }

    public function wasChanged()
    {
        return count($this->getDiffData()) > 0;
    }

    /**
     * Retorna a diferenca entre data e originalData
     */
    public function getDiffData()
    {
        $actual = get_object_vars($this->getData());
        $original = get_object_vars($this->getOriginalData());

        $diff = [];
        foreach ($this->getDiffKeys($original, $actual) as $key) {
            // alterado de null pra string vazia devido a problemas de comparacao
            $originalValue = isset($original[$key]) ? $original[$key] : "";
            $actualValue = isset($actual[$key]) ? $actual[$key] : "";

            // comparando novamente para cobrir os casos acima
            if ($originalValue !== $actualValue) {
                $diff[$key] = [
                    'original' => $originalValue,
                    'change' => $actualValue,
                    'key' => $key
                ];
            }
        }

        return $diff;
    }

    private function getDiffKeys(array $original, array $actual)
    {
        $diff = array_merge(
            array_diff_assoc($actual, $original),
            array_diff_assoc($original, $actual)
        );

        return array_keys($diff);
    }

    /**
     * Retorna os dados originais do model, independente
     * se como o setData influenciou esses campos.
     */
    public function getOriginalData()
    {
        return $this->_originalData ?: new \stdClass();
    }

    protected function getOriginalAttributeValue($attribute) {
        foreach ($this->getDiffData() as $attributeDiff) {
            if ($attributeDiff['key'] == $attribute) {
                return $attributeDiff['original'];
            }
        }

        throw new EModelException("The attribute {$attribute} was not changed!");
    }

    public function attributeWasChanged($attribute)
    {
        try {
            $originalAttributeValue = $this->getOriginalAttributeValue($attribute);
            return isset($originalAttributeValue);
        } catch (EModelException $e) {
            return false;
        }
    }

    /**
     * Recebe um ValueObject com valores planos e inicializa os atributos do Model.
     * @param object $data
     */
    public function setData($data, $role = 'default')
    {
        if (is_null($data)) {
            return;
        }

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_null($role)) {
            $role = 'default';
        }

        $attributes = $this->getAttributesMap();
        foreach ($attributes as $attribute => $definition) {
            if (isset($data[$attribute])) {
                $this->checkAttrMutability($attribute, $role);
                $value = $data[$attribute];
                $type = $definition['type'];
                $conversion = 'get' . $type;
                $typedValue = MTypes::$conversion($value);
                $method = 'set' . $attribute;
                if (method_exists($this, $method)) {
                    $this->set($attribute, $typedValue);
                } else if (method_exists($this->_proxyModel, $method)) {
                    $this->_proxyModel->$method($typedValue);
                }
            }
        }
    }

    private function checkAttrMutability($attribute, $role = 'default')
    {
        $this->validateRole($role);
        if ($this->isImmutable($attribute, $role)) {
            $message = "O atributo {$attribute} não pode ser alterado pelo role {$role}";
            throw new \ESecurityException($message);
        }
    }

    private function isImmutable($attribute, $role)
    {
        return !$this->isWhiteListed($attribute, $role) || $this->isBlackListed($attribute, $role);
    }

    /**
     * Se o desenvolvedor cometer algum erro ao definir o role
     * não quero que isso implique em um relaxamento nas restrições.
     * @param $role
     * @throws Exception
     */
    private function validateRole($role)
    {
        if ($role === 'default') {
            return;
        }

        $blacklist = $this->_getConfig('blacklist');
        $whitelist = $this->_getConfig('whitelist');

        if (!array_key_exists($role, $blacklist) &&
            !array_key_exists($role, $whitelist)
        ) {
            throw new \ESecurityException(
                "O role {$role} não foi definido nas configurações da classe " . get_class($this)
            );
        }
    }

    private function isWhiteListed($attribute, $role)
    {
        $whitelist = $this->_getConfig('whitelist');

        if (empty($whitelist[$role])) {
            return true;
        } else {
            return in_array($attribute, $whitelist[$role]);
        }

    }

    private function isBlackListed($attribute, $role)
    {
        $blacklist = $this->_getConfig('blacklist');
        if (empty($blacklist[$role])) {
            return false;
        } else {
            return in_array($attribute, $blacklist[$role]);
        }
    }

    /**
     * Para evitar a complexidade de ficar testando se a configuração existe ou não.
     * @param $configName
     * @return array
     */
    private function _getConfig($configName)
    {
        if (!isset($this->config()[$configName])) {
            return [];
        }
        return $this->config()[$configName];
    }


    /**
     * Validação dos valores de atributos com base em $config[validators].
     * $exception indica se deve ser disparada uma exceção em caso de falha.
     * @param boolean $exception
     */
    public function validate($exception = true)
    {
        if ($this->_proxyModel) {
            return;
        }
        $validator = new MDataValidator();
        return $validator->validateModel($this, $exception);
    }

    public static function getAllAttributes()
    {
        $allAttributes = static::ORMMap()['attributes'];
        return array_keys($allAttributes);
    }

    public function setOriginalData()
    {
        $this->_originalData = $this->getData();
    }

    function jsonSerialize()
    {
        return $this->getData();
    }

    public function serialize()
    {
        return serialize($this->getData());
    }

    public function unserialize($serialized)
    {
        $this->setData(unserialize($serialized));
    }

}
