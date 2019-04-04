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

class OperandAttributeMap extends PersistentOperand
{

    public $attributeMap;
    public $alias = '';
    public $criteria;
    public $as;

    public function __construct($operand, $name)
    {
        parent::__construct($operand);
        $this->type = 'attributemap';
        if ($p = strpos($name, '.')) {
            $this->alias = substr($name, 0, $p);
        }
        $this->attributeMap = $operand;
    }

    public function getSql()
    {
        return $this->attributeMap->getColumnNameToDb($this->alias);
    }

    public function getSqlName()
    {
        return $this->attributeMap->getName();
    }

    public function getSqlOrder()
    {
        return $this->attributeMap->getFullyQualifiedName($this->alias);
    }

    public function getSqlWhere()
    {
        return $this->attributeMap->getFullyQualifiedName($this->alias);
        //return $this->attributeMap->getColumnWhereName($this->alias);
    }

    public function getSqlGroup()
    {
        return $this->attributeMap->getFullyQualifiedName($this->alias);
    }

}

