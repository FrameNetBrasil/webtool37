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
 * essa classe encapsula o comportamento de um array e serve para a implementação propriedades
 * como coleções na camada de persistência.
 */
class MArray extends MType
    implements \Iterator, \ArrayAccess, \Countable
{
    private $internal;

    public function __construct($internal)
    {
        $this->internal = $this->buildArray($internal);
    }

    private function buildArray($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return get_object_vars($value);
        }

        if (is_scalar($value) || is_null($value)) {
            return [$value];
        }

        throw new \InvalidArgumentException("Não pode converter o valor para um array.");
    }

    public function getValue()
    {
        return $this->internal;
    }

    #region ==ArrayAcess==

    public function offsetExists($offset)
    {
        return isset($this->internal[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->internal[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->internal[] = $value;
        } else {
            $this->internal[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->internal[$offset]);
    }

    #endregion

    #region  == Iterator ==

    public function current()
    {
        return current($this->internal);
    }


    public function next()
    {
        return next($this->internal);
    }

    public function key()
    {
        return key($this->internal);
    }

    public function valid()
    {
        return $this->key() !== null;
    }

    public function rewind()
    {
        return reset($this->internal);
    }

    #endregion

    public function count()
    {
        return count($this->internal);
    }
}