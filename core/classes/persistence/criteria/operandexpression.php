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

class OperandExpression extends PersistentOperand
{

    public $argument;
    public $argOperand;

    public function __construct($criteria, $operand)
    {
        parent::__construct($criteria, $operand);
        $this->type = 'expression';
        $str = $this->argument = $this->operand;
        $separator = " ";
        $tok = strtok($str, $separator);
        while ($tok) {
            $t[$tok] = $tok;
            $tok = strtok($separator);
        }
        foreach ($t as $token) {
            $op = $criteria->getOperand($token);
            if (get_class($op) == 'OperandValue') {
                $op = $criteria->getOperand(':' . $token);
            }
            $this->argument = str_replace($token, $op->getSql(), $this->argument);
        }
    }

    public function getSql()
    {
        return $this->argument;
    }

    public function getSqlGroup()
    {
        return $this->argument;
    }

    public function getSqlOrder()
    {
        return $this->argument;
    }

}

