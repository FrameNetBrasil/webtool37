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

class UpdateCriteria extends DMLCriteria
{

    private $columnAttributes = array();

    public function getSqlStatement()
    {
        $statement = new \database\MSQL();
        $statement->setDb($this->getClassMap()->getDb());
        $statement->setTables($this->classMap->getUpdateSql());

        if (count($this->columnAttributes)) {
            $i = 0;
            $columns = '';

            foreach ($this->columnAttributes as $column) {
                if ($i++)
                    $columns .= ',';

                $columns .= $this->getOperand($column)->getSql();
            }
            $statement->setColumns($columns);
        }

        // Add 'WHERE' clause to update statement
        if (($whereCondition = $this->whereCondition->getSql()) != '') {
            $statement->setWhere($whereCondition);
        }
        return $statement;
    }

    public function addColumnAttribute($attribute)
    {
        $this->columnAttributes[] = $attribute;
    }

    public function update($parameters = null)
    {
        return $this->manager->processCriteriaUpdate($this, $parameters);
    }
}
