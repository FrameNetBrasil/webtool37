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

/**
 * Brief Class Description.
 * Complete Class Description.
 */
class MInvertDate {

    /**
     * Attribute Description.
     */
    public $separator = '/';
    /**
     * Attribute Description.
     */
    var $date;

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $date (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function __construct($date=null) {
        $date = strstr($date, '-') ? str_replace('-', $this->separator, $date) : str_replace('.', $this->separator, $date);
        $this->date = $date;
        $this->formatDate();
    }

    public function formatDate() {
        list($obj1, $obj2, $obj3) = preg_split("#{$this->separator}#", $this->date, 3);
        $this->date = $obj3 . $this->separator . $obj2 . $this->separator . $obj1;
        if (( $this->date == ($this->separator . $this->separator))) {
            $this->date = 'Invalid Date!';
        }
        return $this->date;
    }

}
