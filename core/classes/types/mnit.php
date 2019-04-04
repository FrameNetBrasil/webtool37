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
 * Classe utilitária para trabalhar com CPF.
 * Métodos para formatar e validar strings representando CPF.
 *
 * @category    Maestro
 * @package     Core
 * @subpackage  Types
 * @version     1.0
 * @since       1.0
 */
class MNIT extends MType
{

    /**
     * Valor plano (sem pontuação) do CPF
     * @var string
     */
    private $value;

    public function __construct($value)
    {
        $this->setValue($value);
    }

    public static function create($value)
    {
        return new MNIT($value);
    }

    public function getValue()
    {
        return $this->value ?: '';
    }

    public function setValue($value)
    {
        if (strpos($value, '.') !== false) { // $value está com pontuação
            $value = str_replace('.', '', $value);
            $value = str_replace('-', '', $value);
        }
        $this->value = $value;
    }

    public function isValid()
    {
        return self::validatePISPASEP($this->value);
    }

    public function format()
    {
        return sprintf('%s.%s.%s-%s', substr($this->value, 0, 3), substr($this->value, 3, 5), substr($this->value, 8, 2), substr($this->value, 10, 2));
    }

    public static function validatePISPASEP($pis)
    {
        $pis = preg_replace("/[^0-9]/", '', $pis);

        if (strlen($pis) != 11 || intval($pis) == 0) {
            return false;
        }

        $pisSemDigito = substr($pis, 0, 10);
        $pisSemDigitoInt = (int)$pisSemDigito;

        if ($pisSemDigito == '1111111111' || $pisSemDigito == '2222222222' || $pisSemDigitoInt < 1000000001) {
            return false;
        }

        for ($d = 0, $p = 2, $c = 9; $c >= 0; $c--, ($p < 9) ? $p++ : $p = 2) {
            $d += $pis[$c] * $p;
        }

        return ($pis[10] == (((10 * $d) % 11) % 10));
    }
}
