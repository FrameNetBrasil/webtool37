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

class ConditionCriteria extends BaseCriteria
{

    private $parts = array();
    private $criteria;

    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
    }

    public function getSize()
    {
        return count($this->parts);
    }

    public function add($condition, $conjuntion = 'AND')
    {
        $this->parts[] = array($condition, $conjuntion);
        return $this;
    }

    public function addOr($condition)
    {
        return $this->add($condition, 'OR');
    }

    /**
     * Compatibilidade
     */
    public function addCriteria($conditionCriteria)
    {
        return $this->add($conditionCriteria);
    }

    public function addOrCriteria($conditionCriteria)
    {
        return $this->add($conditionCriteria, 'OR');
    }

    public function addAnd($condition)
    {
        return $this->add($condition, 'AND');
    }

    public function and_($op1, $operator = '', $op2 = NULL)
    {
        if ($op1 instanceof ConditionCriteria) {
            $this->add($op1);
        } else {
            $base = new PersistentCondition($op1, $operator, $op2);
            $base->setCriteria($this->criteria);
            $this->add($base);
        }
        return $this;
    }

    public function or_($op1, $operator = '', $op2 = NULL)
    {
        if ($op1 instanceof ConditionCriteria) {
            $this->addOr($op1);
        } else {
            $base = new PersistentCondition($op1, $operator, $op2);
            $base->setCriteria($this->criteria);
            $this->addOr($base);
        }
        return $this;
    }

    public function getSql()
    {
        $sql = '';
        $n = $this->getSize();

        for ($i = 0; $i < $n; $i++) {
            if ($i != 0) {
                $sql .= " " . $this->parts[$i][1] . " ";
            }
            $condition = $this->parts[$i][0];
            $sql .= $condition->getSql();
        }

        if ($n > 1) {
            $sql = "(" . $sql . ")";
        }
        return $sql;
    }

}
