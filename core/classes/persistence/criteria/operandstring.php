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

class OperandString extends PersistentOperand
{

    public function __construct($operand, $criteria)
    {
        parent::__construct($operand);
        $this->criteria = $criteria;
        $this->type = 'string';
    }

    public function getSql()
    {
        $value = $this->operand;
        $sql = '';
        $tokens = preg_split('/([\s()=]+)/', $value, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($tokens)) {
            foreach ($tokens as $token) {
                $tk = $token;
                $am = $this->criteria->getAttributeMap($tk);
                if ($am instanceof AttributeMap) {
                    $o = new OperandAttributeMap($am, $tk, $this->criteria);
                    $newToken = $o->getSql();
                } else {
                    $tk = $token;
                    if (strrpos($tk, '\\') === false) {
                        $tk = $this->criteria->getClassMap()->getNamespace() . '\\' . $tk;
                    }
                    $cm = $this->criteria->getClassMap($tk);
                    if ($cm instanceof ClassMap) {
                        $newToken = $cm->getTableName();
                    } else {
                        $newToken = $token;
                    }
                }
                $sql .= $newToken;
            }
        } else {
            $sql = $value;
        }
        return $sql;
    }

    public function getSqlWhere()
    {
        $value = $this->operand;
        $sql = '';
        $tokens = preg_split('/([\s()=]+)/', $value, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($tokens)) {
            foreach ($tokens as $token) {
                $tk = $token;
                if ((preg_match("/'(.*)/", $tk) == 0) && (preg_match("/(.*)'/", $tk) == 0)) {
                    $am = $this->criteria->getAttributeMap($tk);
                    if ($am instanceof AttributeMap) {
                        $o = new OperandAttributeMap($am, $tk);
                        $token = $o->getSqlWhere();
                    }
                }
                $sql .= $token;
            }
        } else {
            $sql = $value;
        }
        return $sql;
    }

    public function getSqlGroup()
    {
        $value = $this->operand;
        $sql = '';
        $tokens = preg_split('/([\s()=]+)/', $value, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($tokens)) {
            foreach ($tokens as $token) {
                $tk = $token;
                $am = $this->criteria->getAttributeMap($tk);
                if ($am instanceof AttributeMap) {
                    $o = new OperandAttributeMap($am, $tk);
                    $token = $o->getSqlGroup();
                }
                $sql .= $token;
            }
        } else {
            $sql = $value;
        }
        return $sql;
    }

    public function getSqlOrder()
    {
        $value = $this->operand;
        $sql = '';
        $tokens = preg_split('/([\s()=]+)/', $value, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($tokens)) {
            foreach ($tokens as $token) {
                $tk = $token;
                $am = $this->criteria->getAttributeMap($tk);
                if ($am instanceof AttributeMap) {
                    $o = new OperandAttributeMap($am, $tk);
                    $token = $o->getSqlOrder();
                }
                $sql .= $token;
            }
        } else {
            $sql = $value;
        }
        return $sql;
    }

}

