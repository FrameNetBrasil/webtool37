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
 * Classe utilitária para trabalhar com CNPJ.
 * Métodos para formatar e validar strings representando CNPJ.
 *
 * @category    Maestro
 * @package     Core
 * @subpackage  Types
 * @version     1.0
 * @since       1.0
 */
class MCNPJ extends MType
{

    /**
     * Valor plano (sem pontuação) do CNPJ
     * @var string
     */
    private $value;

    public function __construct($value)
    {
        $this->setValue($value);
    }

    public static function create($value)
    {
        return new MCNPJ($value);
    }

    public function getValue()
    {
        return $this->value ?: '';
    }

    public function setValue($value)
    {
        if (strpos($value, '.') !== false) { // $value está com pontuação
            $value = str_replace('.', '', $value);
            $value = str_replace('/', '', $value);
            $value = str_replace('-', '', $value);
        }
        $this->value = $value;
    }

    static public function validate($value)
    {
        $cnpj = new MCNPJ($value);
        return $cnpj->isValid();
    }

    public function isValid()
    {
        return $this->isCNPJ($this->value);
    }

    public function format()
    {
        $value = $this->value;
        return sprintf('%s.%s.%s/%s-%s', substr($value, 0, 2), substr($value, 2, 3), substr($value, 5, 3), substr($value, 8, 4), substr($value, 12, 2));
    }

    public function getPlainValue()
    {
        return $this->getValue();
    }

    public function __toString()
    {
        return $this->format();
    }

    function isCNPJ($cnpj)
    {
        //Etapa 1: Cria um array com apenas os digitos numéricos, isso permite receber o cnpj em diferentes formatos como "00.000.000/0000-00", "00000000000000", "00 000 000 0000 00" etc...
        $j = 0;
        for ($i = 0; $i < (strlen($cnpj)); $i++) {
            if (is_numeric($cnpj[$i])) {
                $num[$j] = $cnpj[$i];
                $j++;
            }
        }
        //Etapa 2: Conta os dígitos, um Cnpj válido possui 14 dígitos numéricos.
        if (count($num) != 14) {
            $isCnpjValid = false;
        }
        //Etapa 3: O número 00000000000 embora não seja um cnpj real resultaria um cnpj válido após o calculo dos dígitos verificares e por isso precisa ser filtradas nesta etapa.
        if ($num[0] == 0 && $num[1] == 0 && $num[2] == 0 && $num[3] == 0 && $num[4] == 0 && $num[5] == 0 && $num[6] == 0 && $num[7] == 0 && $num[8] == 0 && $num[9] == 0 && $num[10] == 0 && $num[11] == 0) {
            $isCnpjValid = false;
        } //Etapa 4: Calcula e compara o primeiro dígito verificador.
        else {
            $j = 5;
            for ($i = 0; $i < 4; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $j = 9;
            for ($i = 4; $i < 12; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $resto = $soma % 11;
            if ($resto < 2) {
                $dg = 0;
            } else {
                $dg = 11 - $resto;
            }
            if ($dg != $num[12]) {
                $isCnpjValid = false;
            }
        }
        //Etapa 5: Calcula e compara o segundo dígito verificador.
        if (!isset($isCnpjValid)) {
            $j = 6;
            for ($i = 0; $i < 5; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $j = 9;
            for ($i = 5; $i < 13; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }
            $soma = array_sum($multiplica);
            $resto = $soma % 11;
            if ($resto < 2) {
                $dg = 0;
            } else {
                $dg = 11 - $resto;
            }
            if ($dg != $num[13]) {
                $isCnpjValid = false;
            } else {
                $isCnpjValid = true;
            }
        }
        //Trecho usado para depurar erros.
        /*
          if($isCnpjValid==true)
          {
          echo "<p><font color=\"GREEN\">Cnpj é Válido</font></p>";
          }
          if($isCnpjValid==false)
          {
          echo "<p><font color=\"RED\">Cnpj Inválido</font></p>";
          }
         */
        //Etapa 6: Retorna o Resultado em um valor booleano.
        return $isCnpjValid;
    }

}
