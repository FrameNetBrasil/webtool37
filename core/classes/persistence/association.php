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

class Association extends ArrayObject
{

    private $classMap;
    private $manager;
    private $baseObject;
    private $index;

    public function offsetGet($name)
    {
        return call_user_func_array(array(parent, __FUNCTION__), func_get_args());
    }

    public function offsetSet($name, $value)
    {
        return call_user_func_array(array(parent, __FUNCTION__), func_get_args());
    }

    public function offsetExists($name)
    {
        return call_user_func_array(array(parent, __FUNCTION__), func_get_args());
    }

    public function offsetUnset($name)
    {
        return call_user_func_array(array(parent, __FUNCTION__), func_get_args());
    }

    public function __construct(Classmap $classMap, $index = NULL)
    {
        $this->classMap = $classMap;
        $this->baseObject = $this->classMap->getObject();
        $this->manager = PersistentManager::getInstance();
        $this->index = $index;
    }

    public function init($query, $index = NULL)
    {
        $index = $index ?: $this->index;
        $query->moveFirst();
        while (!$query->eof()) {
            $object = $this->classMap->getObject();//clone $this->baseObject;
            $data = $query->getRowObject();
            $this->classMap->setObject($object, $data);
            $object->setPersistent(true);
            $object->setOriginalData();

            if (is_null($index)) {
                $this->append($object);
            } else {
                $this->offsetSet($object->get($index), $object);
            }
            $query->moveNext();
        }
    }

    public function getModels()
    {
        $models = array();
        if ($this->count()) {
            foreach ($this as $model) {
                $models[$model->getId()] = $model;
            }
        }
        return $models;
    }

    public function getObjects()
    {
        $index = $index ?: $this->index;
        $objects = array();
        if ($this->count()) {
            foreach ($this as $model) {
                if (is_null($index)) {
                    $objects[] = $model->getData();
                } else {
                    $objects[$model->get($index)] = $model->getData();
                }
            }
        }
        return $objects;
    }

    public function getId()
    {
        $id = array();
        if ($this->count()) {
            foreach ($this as $model) {
                $id[] = $model->getId();
            }
        }
        return $id;
    }

    public function walk(callable $operation)
    {
        if ($this->count()) {
            foreach ($this as $model) {
                call_user_func($operation, $model->getId(), $model);
            }
        }
    }

}
