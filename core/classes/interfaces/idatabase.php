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

interface IDataBase
{
    public function __construct($name);

    public function newConnection();

    public function getConnection();

    public function getConfig($key);

    public function getName();

    public function getPlatform();

    public function getORMLogger();

    public function getTransaction();

    public function lastInsertId();

    public function beginTransaction();

    public function getSQL($columns, $tables, $where, $orderBy, $groupBy, $having, $forUpdate);

    public function execute(database\MSQL $sql, $parameters);

    public function executeBatch($sqlArray);

    public function executeCommand($command, $parameters);

    public function count(database\MQuery $query);

    public function getNewId($sequence);

    public function prepare(database\MSQL $sql);

    public function query(database\MSQL $sql);

    public function executeQuery($command, $parameters, $page, $rows);

    public function getQueryCommand($command);

    public function getQuery(database\MSQL $sql);

    public function getTable($tableName);

    public function executeProcedure($sql, $aParams, $aResult);
}
