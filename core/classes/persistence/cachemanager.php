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
 * Classe para gerência de objetos em cache
 * User: Marcello
 * Date: 02/09/2016
 * Time: 16:03
 */
class CacheManager
{
    private static $instance;
    private $cache;

    private function __construct(MCacheService $cache)
    {
        $this->cache = $cache;
    }

    public static final function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new CacheManager(MCacheService::getCacheService());
        }

        return self::$instance;
    }

    public function save(\PersistentObject $object)
    {
        if ($this->isCacheable($object)) {
            $key = $this->buildCacheKey($object);
            $this->cache->set($key, $object->getData(), $this->getTTL($object));
            $this->showTraceMessage("Object cached", $object, $key);
        }
    }

    public function delete(\PersistentObject $object)
    {
        if ($this->isCacheable($object)) {
            $key = $this->buildCacheKey($object);
            $this->cache->delete($key);
            $this->showTraceMessage("Object removed from cache", $object, $key);
        }
    }

    /**
     * Verifica se o objeto possui os pré requisitos necessários para ser armazenado em cache.
     * @param PersistentObject $object
     * @return bool
     */
    public function isCacheable(\PersistentObject $object)
    {
        return ($object instanceof MBusinessModel
            && $this->cacheIsConfigured($object));
    }

    /**
     * Verifica a configuração do objeto para definir se ele usa cache.
     * @param MBusinessModel $object
     * @return bool
     */
    private function cacheIsConfigured(MBusinessModel $object)
    {
        $config = $object->config();
        return isset($config['cache']) && ($config['cache'] === true || count($config['cache']) > 0);
    }

    private function getTTL(\MBusinessModel $model)
    {
        $config = $model->config();
        return isset($config['cache']['ttl']) ? $config['cache']['ttl'] : -1;
    }

    public function clear()
    {
        $keys = $this->cache->getKeys("siga:*");
        $this->cache->deleteMultiple($keys);
    }

    public function cacheIsEnabled()
    {
        return $this->cache->serviceIsAvailable();
    }

    /**
     * Preenche o objeto do modelo com os dados da cache.
     *
     * @param PersistentObject $object
     * @return bool
     */
    public function load(\PersistentObject $object, $id)
    {
        if (!$this->isCacheable($object) && $id) {
            return false;
        }

        $key = $this->buildCacheKey($object);
        $data = $this->cache->get($key);
        if (!$data) {
            $this->showTraceMessage("CacheMiss", $object, $key);
            return false;
        } else {
            $this->setDataAsString($data, $object);
            $object->setPersistent(true);
            $this->showTraceMessage("CacheHit", $object, $key);
            return true;
        }
    }

    private function showTraceMessage($message, PersistentObject $object, $key)
    {
        mtrace($message . ' ' . get_class($object)
            . ' id=' . $object->getId()
            . ' key=' . $key
        );
    }

    /**
     * Seta todos os valores no model como strings.
     *
     * Essa função é necessária porque na primeira instanciação, os objetos MBusinessModel tem todas as suas propriedades
     * do tipo string. Na chamada do getData, esses métodos são convertidos para seu tipo correto.
     * Como quero entregar uma cópia exatamente igual ao que resultaria da chamada de um retrieve() estou fazendo esse
     * trabalho extra aqui.
     *
     * @param $data
     */
    private function setDataAsString($data, $model)
    {
        $properties = get_object_vars($data);
        foreach ($properties as $property => $value) {
            $method = 'set' . $property;
            if (method_exists($model, $method)) {
                $model->$method("$value");
            }
        }
    }

    private function buildCacheKey(\PersistentObject $persistent)
    {
        $class = get_class($persistent);
        $id = $persistent->getId();
        //hash da classe para diferenciar classes com mesmo nome e namespaces diferentes
        $uid = strtoupper(md5("$class::$id"));
        $classSimple = end(explode('\\', $class));

        return "siga:{$classSimple}:" . $uid;
    }

}