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

class OperandObject extends PersistentOperand
{

    private $criteria;

    public function __construct($operand, $criteria)
    {
        parent::__construct($operand);
        $this->type = 'object';
        $this->criteria = $criteria;
    }

    public function getSql()
    {
        if (method_exists($this->operand, 'getSql')) {
            return $this->operand->getSql();
        } else { // se não existe o método getSql, acrescenta como parâmetro nomeado
            $name = uniqid('param_');
            $this->criteria->addParameter($this->operand, $name);
            return ':' . $name;
        }
    }

    public function getSqlWhere()
    {
        $platform = $this->criteria->getClassMap()->getPlatform();
        return $platform->convertWhere($this->operand);
    }
}

