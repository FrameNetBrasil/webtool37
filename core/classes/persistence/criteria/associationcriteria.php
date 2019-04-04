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

class AssociationCriteria
{

    private $name;
    private $associationMap;
    private $joinType;
    private $tables;
    private $alias;
    private $persistentCriteria;

    public function __construct($name, $criteria, $joinType = 'INNER')
    {
        $this->name = $name;
        $this->joinType = $joinType;
        $this->persistentCriteria = $criteria;
    }

    public function setCriteria($criteria)
    {
        $this->persistentCriteria = $criteria;
    }

    public function getCriteria()
    {
        return $this->persistentCriteria;
    }

    public function setAssociationMap($associationMap)
    {
        $this->associationMap = $associationMap;
        //if ($associationMap instanceof AssociationMap) {
        //    if ($associationMap->isAutoAssociation()) {
        //        $this->alias = $associationMap->getName();
        //$this->getCriteria()->setAlias($this->alias, $associationMap->getToClassMap());
        //    }
        //}
    }

    public function getAssociationMap()
    {
        return $this->associationMap;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getJoinType()
    {
        return $this->joinType;
    }

    public function setJoinType($joinType)
    {
        $this->joinType = $joinType;
    }

    public function getJoin()
    {
        $this->associationMap->setKeysAttributes();
        $cardinality = $this->associationMap->getCardinality();
        if ($cardinality == 'manyToMany') {
            $associativeTable = $this->associationMap->getAssociativeTable();
            $names = $this->associationMap->getNames();
            $condition = $names->fromColumnName . "=" . $associativeTable . '.' . $names->fromColumn;
            $join[] = array($names->fromTable, $associativeTable, $condition, $this->joinType);
            $condition = $associativeTable . '.' . $names->toColumn . "=" . $names->toColumnName;
            $join[] = array($associativeTable, $names->toTable, $condition, $this->joinType);
        } else {
            //$fromAlias = ($this->associationMap->isAutoAssociation() ? '' : $this->persistentCriteria->getAlias($this->associationMap->getFromClassName()));
            $fromAlias = $this->persistentCriteria->getAlias($this->associationMap->getFromClassName());
            $toAlias = $this->alias;
            $names = $this->associationMap->getNames($fromAlias, $toAlias);
            $condition = $names->fromColumnName . "=" . $names->toColumnName;
            $join[] = array($names->fromTable, $names->toTable, $condition, $this->joinType);
        }
        return $join;
    }

}
