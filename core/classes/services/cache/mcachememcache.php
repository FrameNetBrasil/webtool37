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

class MCacheMemCache extends MService
{
    public $defaulTTL;
    public $memcache;
    public $sessionid;

    public function __construct()
    {
        parent::__construct();
        $this->memcache = new MemCache;
        if (!$this->memcache->connect($this->getConf('cache.memcache.host'), $this->getConf('cache.memcache.port'))) {
            die('Could not connect to MemCache!');
        }
        $this->defaultTTL = $this->getConf('cache.memcache.default.ttl');
        $this->sessionid = $this->manager->getSession()->getId();
    }

    public function add($name, $value, $ttl = 0)
    {
        $key = md5($this->sessionid . $name);
        $this->memcache->add($key, $value, '', $ttl ? $ttl : $this->defaultTTL);
    }

    public function set($name, $value, $ttl = 0)
    {
        $key = md5($this->sessionid . $name);
        $result = $this->memcache->set($key, $value, MEMCACHE_COMPRESSED, $ttl ? $ttl : $this->defaultTTL);
    }

    public function get($name)
    {
        $key = md5($this->sessionid . $name);
        return $this->memcache->get($key);
    }

    public function delete($name)
    {
        $key = md5($this->sessionid . $name);
        $this->memcache->delete($key);
    }

    public function clear()
    {
        $this->memcache->flush();
        $time = time() + 1; //one second future
        while (time() < $time) {
            //sleep
        }
    }
}
