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

class MCurrencyFormatter extends CurrencyFormatter
{
    public $defaultISOCode = 'REAL';

    public function __construct($ISOCode = NULL)
    {
        parent::__construct();
        if (is_null($ISOCode)) {
            $ISOCode = $this->defaultISOCode;
        }
    }

    function format($amount, $ISOCode = NULL)
    {
        if (is_null($ISOCode)) {
            $ISOCode = $this->defaultISOCode;
        }
        return parent::format($amount, $ISOCode);
    }

    function formatWithSymbol($amount, $ISOCode = NULL)
    {
        if (is_null($ISOCode)) {
            $ISOCode = $this->defaultISOCode;
        }
        return parent::formatWithSymbol($amount, $ISOCode);
    }

    function validate($amount, $ISOCode = NULL)
    {
        if (is_null($ISOCode)) {
            $ISOCode = $this->defaultISOCode;
        }
        return parent::validate($amount, $ISOCode);
    }

    function toDecimal($amount, $ISOCode = NULL)
    {
        if (is_float($amount)) return $amount;
        if (strrpos($amount, '.') > strrpos($amount, ',')) {
            $amount = str_replace(',', '', $amount);
            $amount = str_replace('.', ',', $amount);
        }
        if (is_null($ISOCode)) {
            $ISOCode = $this->defaultISOCode;
        }
        return parent::toDecimal($amount, $ISOCode);
    }
}
