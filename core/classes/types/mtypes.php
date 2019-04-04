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
 * Classe utilitária para conversão de tipos.
 * Esta classe contém métodos estáticos para conversão de tipos simples (planos)
 * em tipos complexos e vice-versa.
 * Tipos simples: integer, boolean, string, character, float
 * Tipos complexos: date (MDate), timestamp (MTimeStamp), currency (MCurrency),
 * blob (MFile), text, cpf (MCPF) e cnpj (MCNPJ).
 * A conversão do tipo complexo em plano é feita através da chamada padrão
 * complexo::getPlainValue().
 * @see MBusinessModel::setData
 *
 * @category    Maestro
 * @package     Core
 * @subpackage  Types
 * @version     1.0
 * @since       1.0
 */
class MTypes
{

    /*
     * Override para Enumerações
     */

    public static function __callStatic($name, $value)
    {
        if (strpos(strtolower($name), 'enum') !== false) {
            return $value[0];
        }
    }

    /*
     * Métodos para conversão de valores: plain -> tipo
     */

    /**
     * Converte para inteiro.
     * @param $plainValue Valor a ser convertido para inteiro.
     * @return int|null Valor convertido.
     */
    public static function getInteger($plainValue)
    {
        return $plainValue === '' ? NULL : (integer)$plainValue;
    }

    public static function getBoolean($plainValue)
    {
        return (boolean)$plainValue;
    }

    public static function getString($plainValue)
    {
        return (string)$plainValue;
    }

    public static function getCharacter($plainValue)
    {
        return (string)$plainValue;
    }

    public static function getDate($plainValue, $format = '')
    {
        return new MDate($plainValue, $format);
    }

    public static function getTimestamp($plainValue, $format = '')
    {
        return new MTimestamp($plainValue, $format);
    }

    public static function getCurrency($plainValue)
    {
        return new MCurrency($plainValue);
    }

    public static function getCPF($plainValue)
    {
        return new MCPF($plainValue);
    }

    public static function getCNPJ($plainValue)
    {
        return new MCNPJ($plainValue);
    }

    public static function getBLOB($plainValue)
    {
        return $plainValue;
    }

    public static function getCLOB($plainValue)
    {
        return $plainValue;
    }

    public static function getText($plainValue)
    {
        return $plainValue;
    }

    public static function getFloat($plainValue)
    {
        return $plainValue === '' ? NULL : (float)$plainValue;
    }

    /*
     * Métodos para conversão de valores: tipo -> plain
     */

    public static function getPlainInteger($value)
    {
        return (integer)$value;
    }

    public static function getPlainNumber($value)
    {
        return (number_format($value));
    }

    public static function getPlainBoolean($value)
    {
        return (boolean)$value;
    }

    public static function getPlainString($value)
    {
        return $value;
    }

    public static function getPlaincharacter($value)
    {
        return $value;
    }

    public static function getPlainDate($value)
    {
        return $value->getPlainValue();
    }

    public static function getPlainTimestamp($value)
    {
        return $value->getPlainValue();
    }

    public static function getPlainCurrency($value)
    {
        return $value->getPlainValue();
    }

    public static function getPlainCPF($value)
    {
        return $value->getPlainValue();
    }

    public static function getPlainCNPJ($value)
    {
        return $value->getPlainValue();
    }

    public static function getPlainBLOB($value)
    {
        return $value->getPlainValue();
    }

    public static function getPlainText($value)
    {
        return $value;
    }

    public static function getPlainFloat($value)
    {
        return (float)$value;
    }

    public static function getPlainClob($value)
    {
        return is_a($value, '\OCI-Lob') ? $value->load() : $value;
    }

}
