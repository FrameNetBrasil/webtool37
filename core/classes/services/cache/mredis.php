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
 * Serviço de cache que utiliza o Redis como base.
 */
class MRedis extends \MCacheService
{
    private static $instance;
    private $redis;
    private $isAvailable;

    private function __construct()
    {
        $this->init();
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    protected function init()
    {
        $host = Manager::getConf('cache.Redis.host');
        $port = Manager::getConf('cache.Redis.port');
        $this->redis = new \Redis();
        $this->isAvailable = $this->redis->connect($host, $port);
    }

    public function getRedis()
    {
        return $this->redis;
    }

    public function add($name, $value, $ttl = -1)
    {
        $this->set($name, $value, $ttl);
    }

    public function set($name, $value, $ttl = -1)
    {
        if ($ttl < 0) {
            $ttl = Manager::getConf('cache.Redis.expirationDefault');
        }

        return $this->redis->set($name, serialize($value), $ttl);
    }

    public function increment($name, $by = 1)
    {
        return $this->redis->incrBy($name, $by);
    }

    public function decrement($name, $by = 1)
    {
        return $this->redis->decrBy($name, $by);
    }

    public function get($name)
    {
        return unserialize($this->redis->get($name));
    }

    public function delete($name)
    {
        return $this->redis->delete($name);
    }

    public function clear()
    {
        return $this->redis->flushAll();
    }

    public function getAllKeys()
    {
        return $this->getKeys();
    }

    public function getKeys($pattern = '*')
    {
        return $this->redis->keys($pattern);
    }

    public function deleteMultiple(array $keys)
    {
        return $this->redis->delete($keys);
    }

    public function serviceIsAvailable()
    {
        return $this->isAvailable;
    }
}