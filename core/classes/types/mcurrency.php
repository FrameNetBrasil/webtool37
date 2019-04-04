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

class MCurrency extends MType
{

    private $value;

    public function __construct($value)
    {
        $this->setValue($value);
    }

    private function getValueFromString($value)
    {
        $l = localeConv();
        $sign = (strpos($value, $l['negative_sign']) !== false) ? -1 : 1;
        $value = strtr($value, $l['positive_sign'] . $l['negative_sign'] . '()', '    ');
        $value = str_replace(' ', '', $value);
        $value = str_replace($l['currency_symbol'], '', $value);
        $value = str_replace($l['mon_thousands_sep'], '', $value);
        $value = str_replace($l['mon_decimal_point'], '.', $value);
        return (float)($value * $sign);
    }

    public function getValue()
    {
        return $this->value ?: (float)0.0;
    }

    public function setValue($value)
    {
        if ($value instanceof MCurrency) {
            $value = $value->getValue();
        }
        $this->value = (is_numeric($value) ? round($value, 2, PHP_ROUND_HALF_DOWN) : (is_string($value) ? $this->getValueFromString($value) : 0.0));
    }

    public function format()
    {
        $l = localeConv();
        // Sign specifications:
        if ($this->value >= 0) {
            $sign = $l['positive_sign'];
            $sign_posn = $l['p_sign_posn'];
            $sep_by_space = $l['p_sep_by_space'];
            $cs_precedes = $l['p_cs_precedes'];
        } else {
            $sign = $l['negative_sign'];
            $sign_posn = $l['n_sign_posn'];
            $sep_by_space = $l['n_sep_by_space'];
            $cs_precedes = $l['n_cs_precedes'];
        }
        // Currency format:
        $m = number_format(abs($this->value), $l['frac_digits'], $l['mon_decimal_point'], $l['mon_thousands_sep']);
        if ($sep_by_space) {
            $space = ' ';
        } else {
            $space = '';
        }
        if ($cs_precedes) {
            $m = $l['currency_symbol'] . $space . $m;
        } else {
            $m = $m . $space . $l['currency_symbol'];
        }
        switch ($sign_posn) {
            case 0:
                $m = "($m)";
                break;
            case 1:
                $m = "$sign$m";
                break;
            case 2:
                $m = "$m$sign";
                break;
            case 3:
                $m = "$sign$m";
                break;
            case 4:
                $m = "$m$sign";
                break;
            default:
                $m = "$m [error sign_posn=$sign_posn&nbsp;!]";
        }
        return $m;
    }

    public function formatValue()
    {
        $l = localeConv();
        // Sign specifications:
        if ($this->value >= 0) {
            $sign = $l['positive_sign'];
            $sign_posn = $l['p_sign_posn'];
        } else {
            $sign = $l['negative_sign'];
            $sign_posn = $l['n_sign_posn'];
        }
        // Currency format:
        $m = number_format(abs($this->value), $l['frac_digits'], $l['mon_decimal_point'], $l['mon_thousands_sep']);
        switch ($sign_posn) {
            case 0:
                $m = "($m)";
                break;
            case 1:
                $m = "$sign$m";
                break;
            case 2:
                $m = "$m$sign";
                break;
            case 3:
                $m = "$sign$m";
                break;
            case 4:
                $m = "$m$sign";
                break;
            default:
                $m = "$m [error sign_posn=$sign_posn&nbsp;!]";
        }
        return $m;
    }

    public function getPlainValue()
    {
        return $this->getValue();
    }

    public function __toString()
    {
        return $this->format();
    }

    public function getExtension($lang)
    {
        $valor = $this->getValue();
        if (strpos($valor, ",") > 0) {
            // retira o ponto de milhar, se tiver
            $valor = str_replace(".", "", $valor);

            // troca a virgula decimal por ponto decimal
            $valor = str_replace(",", ".", $valor);
        }

        //obtem o arquivo de configuração e linguagem
        $lang = Manager::getOptions('language');
        $file = 'messages.' . ($lang ? $lang . '.' : '') . 'php';
        $file = Manager::getFrameworkPath('conf/' . $file);
        $currencyExt = file_exists($file) ? require($file) : array();

        $currencyExt = $currencyExt['currencyExtension'];

        $singular = $currencyExt['classSingular'];
        $plural = $currencyExt['classPlural'];

        $c = $currencyExt['orderHundred'];
        $d = $currencyExt['orderDozen'];
        $d10 = $currencyExt['firstDozen'];
        $u = $currencyExt['orderUnit'];
        $z = 0;
        $ot = $currencyExt['other'];

        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
        $cont = count($inteiro);
        for ($i = 0; $i < $cont; $i++)
            for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
                $inteiro[$i] = "0" . $inteiro[$i];

        $fim = $cont - ($inteiro[$cont - 1] > 0 ? 1 : 2);
        $rt = '';
        for ($i = 0; $i < $cont; $i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? $ot[1] : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " " . $ot[2] . " " : "") . $rd . (($rd &&
                    $ru) ? " " . $ot[2] . " " : "") . $ru;
            $t = $cont - 1 - $i;
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($valor == "000"

            ) $z++; elseif ($z > 0)
                $z--;
            if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
                $r .= (($z > 1) ? " " . $ot[3] . " " : "") . $plural[$t];
            if ($r)
                $rt = $rt . ((($i > 0) && ($i <= $fim) &&
                        ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " " . $ot[2] . " ") : " ") . $r;
        }
        return ($rt ? $rt : $ot[0]);
    }

    public function equals($currency)
    {
        $currency = new self($currency);
        return $this->getValue() == $currency->getValue();
    }

    public function plus($currency)
    {
        $currency = new self($currency);
        return new self($this->getValue() + $currency->getValue());
    }

    public function minus($currency)
    {
        $currency = new self($currency);
        return new self($this->getValue() - $currency->getValue());
    }

    public function times($value)
    {
        return new self($this->getValue() * $value);
    }

    public function div($value)
    {
        if ($value == 0) {
            throw new \InvalidArgumentException("Divide by zero!");
        }

        return new self($this->getValue() / $value);
    }

}
