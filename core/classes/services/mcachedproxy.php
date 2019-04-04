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
 * Essa classe visa a implementação de cache de forma transparente.
 * User: Marcello
 * Date: 08/09/2016
 * Time: 10:23
 */
class MCachedProxy
{
    private $object;
    private $cache;
    private $expiration;

    private function __construct($object)
    {
        $this->object = $object;
        $this->cache = \MCacheService::getCacheService();
        $this->expiration = 60 * 60 * 24;
    }

    public static function proxify($object)
    {
        return new self($object);
    }

    public function setExpiration($seconds)
    {
        $this->expiration = $seconds;
        return $this;
    }

    public function __call($name, $parameters)
    {
        $key = $this->buildKey($name, $parameters);
        $result = $this->cache->get($key);

        if (!$result) { //cache miss
            $result = call_user_func_array([$this->object, $name], $parameters);
            $this->cache->set($key, $result, $this->expiration);
        }

        return $result;
    }

    private function buildKey($name, $arguments)
    {
        $join = get_class($this->object) . $name . serialize($arguments);
        return 'siga:' . md5($join);
    }

}