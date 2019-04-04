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

class BaseCriteria
{

    public function getOperand($operand, $accentInsensitive = false)
    {
        if (is_null($operand)) {
            $o = new OperandNull($operand);
        } elseif (is_object($operand)) {
            if ($operand instanceof AttributeMap) {
                $o = new OperandAttributeMap($operand, $operand->getName());
            } elseif ($operand instanceof RetrieveCriteria) {
                $o = new OperandCriteria($operand, $this);
            } else {
                $o = new OperandObject($operand, $this);
            }
        } elseif (is_array($operand)) {
            $o = new OperandArray($operand);
        } else {
            $o = $accentInsensitive ? new OperandStringAI($operand, $this) : new OperandString($operand, $this);
        }
        return $o;
    }

    public function getTableName($className)
    {
        $manager = PersistentManager::getInstance();
        $classMap = $manager->getClassMap($className);
        return $classMap->getTableName();
    }

    public function getCondition($op1, $operator = '', $op2 = NULL)
    {
        $criteria = new ConditionCriteria();
        if ($op1 instanceof ConditionCriteria) {
            $criteria->add($op1);
        } elseif ($op1 instanceof PersistentCondition) {
            $criteria->add($op1);
        } else {
            $base = new PersistentCondition($op1, $operator, $op2);
            $base->setCriteria($criteria);
            $criteria->add($base);
        }
        return $criteria;
    }
}
