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
 * Classe que utiliza o padrão Null Object para situações onde a cache não está configurada.
 */
class MNullCache extends \MCacheService
{
    public function add($name, $value, $ttl = 0)
    {
        return true;
    }

    public function set($name, $value, $ttl = 0)
    {
        return true;
    }

    public function get($name)
    {
        return false;
    }

    public function delete($name)
    {
        return true;
    }

    public function clear()
    {
        return true;
    }

    public function getKeys($pattern = '*')
    {
        return [];
    }

    public function getAllKeys()
    {
        return [];
    }

    public function deleteMultiple(array $keys)
    {
        return true;
    }

    public function increment($name, $by = 1)
    {
        return true;
    }

    public function decrement($name, $by = 1)
    {
        return true;
    }

    public function serviceIsAvailable()
    {
        return false;
    }
}