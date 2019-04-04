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

interface IPersistentManager
{

    public function retrieveObject(PersistentObject $object);

    public function retrieveObjectFromQuery(PersistentObject $object, Database\MQuery $query);

    public function retrieveObjectFromCriteria(PersistentObject $object, PersistentCriteria $criteria, $parameters);

    public function retrieveAssociation(PersistentObject $object, $associationName);

    public function retrieveAssociationAsCursor(PersistentObject $object, $target);

    public function getClassMap($className, $mapClassName);

    public function getConnection($dbName);

    public function getDeleteCriteria(PersistentObject $object);

    public function getRetrieveCriteria(PersistentObject $object, $command);

    public function getUpdateCriteria(PersistentObject $object);

    public function getValue($object, $attribute);

    public function saveObjectRaw(PersistentObject $object);

    public function saveObject(PersistentObject $object);

    public function saveAssociation(PersistentObject $object, $associationName);

    public function saveAssociationById(PersistentObject $object, $associationName, $id);

    public function deleteObject(PersistentObject $object);

    public function deleteAssociation(PersistentObject $object, $associationName);

    public function deleteAssociationObject(PersistentObject $object, $associationName, PersistentObject $refObject);

    public function deleteAssociationById(PersistentObject $object, $associationName, $id);
}
