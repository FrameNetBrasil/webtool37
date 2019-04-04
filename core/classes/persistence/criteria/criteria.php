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

class Criteria
{

    public static function getOperand($operand, $criteria = NULL)
    {
        if ($operand == NULL) {
            $o = new OperandNull($operand);
        } elseif (is_object($operand)) {
            if ($operand instanceof AttributeMap) {
                $o = new OperandAttributeMap($operand, $operand->getName());
            } elseif ($operand instanceof RetrieveCriteria) {
                $o = new OperandCriteria($operand);
            } else {
                $o = new OperandObject($operand);
            }
        } elseif (is_array($operand)) {
            $o = new OperandArray($operand);
//        } elseif (substr($operand, 0, 1) == '!') {
//            $o = new OperandExpression($this, substr($operand, 1));
        } else {
            $o = new OperandString($operand, $criteria);
        }
        /*
    } elseif (strpos($operand, '(') === FALSE) {
        $op = $operand;
        if (($c = $criteria->tableCriteriaColumn[$op]) != '') {
            $op = ':' . $c;
            $o = new OperandValue($op);
        } else {
            $am = $criteria->getAttributeMap($operand);
            if ($am == NULL) {
                $o = new OperandValue($op);
            } else {
                $o = new OperandAttributeMap($am, $operand, $criteria);
            }
        }
    } else {
        $o = new OperandFunction($operand, $criteria);
    }
         *
         */
        return $o;
    }

    public static function getTableName($className)
    {
        $manager = PersistentManager::getInstance();
        $classMap = $manager->getClassMap($className);
        return $classMap->getTableName();
    }

    public static function condition($op1, $operator = '', $op2 = NULL)
    {
        $criteria = new CriteriaCondition();
        if ($op1 instanceof CriteriaCondition) {
            $criteria->add($op1);
        } elseif ($op1 instanceof BaseCriteria) {
            $criteria->add($op1);
        } else {
            $base = new BaseCriteria($op1, $operator, $op2);
            $criteria->add($base);
        }
        return $criteria;
    }


}
