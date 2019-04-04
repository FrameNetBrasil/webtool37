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

class MCacheAPC extends MService
{
    public $defaulTTL;

    public function __construct()
    {
        parent::__construct();
        if (!function_exists('apc_cache_info') || !($cache = @apc_cache_info($cache_mode))) {
            echo "No cache info available.  APC does not appear to be running.";
            exit;
        }
        $this->defaultTTL = $this->getConf('cache.apc.default.ttl');
    }

    public function add($name, $value, $ttl = 0)
    {
        $value = serialize($value);
        apc_add($name, $value, $ttl ? $ttl : $this->defaultTTL);
    }

    public function set($name, $value, $ttl = 0)
    {
        $value = serialize($value);
        apc_store($name, $value, $ttl ? $ttl : $this->defaultTTL);
    }

    public function get($name)
    {
        $value = apc_fetch($name);
        return unserialize($value);
    }

    public function delete($name)
    {
        apc_delete($name);
    }

    public function clear()
    {
        apc_clear_cache();
    }

}

