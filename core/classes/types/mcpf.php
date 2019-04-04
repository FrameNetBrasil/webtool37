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
class MCPF extends MType
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
        return new MCPF($value);
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

    static public function validate($value)
    {
        return $value->isValid();
    }

    public function isValid()
    {
        return $this->validateCPF();
    }

    public function format()
    {
        return sprintf('%s.%s.%s-%s', substr($this->value, 0, 3), substr($this->value, 3, 3), substr($this->value, 6, 3), substr($this->value, 9, 2));
    }

    public function getPlainValue()
    {
        return $this->getValue();
    }

    public function __toString()
    {
        return $this->format();
    }

    private function validateCPF()
    {
        $cpf = $this->value;
        if (preg_match('/[0-9]{9}YY/', $cpf) == 1) {
            return true;
        }
        // Verifiva se o número digitado contém todos os digitos
        $cpf = str_pad(ereg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);
        // Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
            return false;
        } else {   // Calcula os números para verificar se o CPF é verdadeiro
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }
            return true;
        }
    }

}
