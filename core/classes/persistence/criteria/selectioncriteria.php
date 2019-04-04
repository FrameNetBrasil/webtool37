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

class SelectionCriteria
{
    public $attributeMap;
    public $operator;
    public $value;

    public function selectionCriteria($attributeMap, $operator, $value)
    {
        $this->attributeMap = $attributeMap;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function _getValue()
    {
        if (is_array($this->value)) {
            $value = "(";
            $i = 0;

            foreach ($this->value as $v) {
                $value .= ($i++ > 0) ? ", " : "";
                $value .= "'$v'";
            }

            $value .= ")";
        } elseif (is_object($this->value)) {
            if ($this->value instanceof RetrieveCriteria) {
                $value = "(" . $this->value->getSqlStatement()->select() . ")";
            }
        } else {
            $value = $this->value;
        }

        return $value;
    }

    public function setWhereStatement($statement)
    {
        $condition = "(";
        $condition .= $this->attributeMap->getColumnMap()->getColumnName() . ' ' . $this->operator . ' ' . $this->_getValue();
        $condition .= ")";
        $statement->setWhere($condition);
    }

    public function getWhereSql()
    {
        $condition = "(";
        $conv = $cm->getConverter();
        $condition .= $this->attributeMap->getColumnMap()->getColumnName() . ' ' . $this->operator . ' ' . $this->_getValue();
        $condition .= ")";
        return $condition;
    }
}
