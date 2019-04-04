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
 * Handles the formatting of monetary amounts according to different formatting styles.
 *
 * An amount can be formatted according to a given style, or to the style for a given
 * ISO 4217 currency code (eg. AUD).
 *
 * The class can also be used to validate currency amounts according to a particular formatting
 * style, and to convert a formatted string to a double amount.
 *
 * Example usage:
 *
 * -------------------------------------------------------------------------
 * $cf = new CurrencyFormatter();
 *
 * $formattedAmount = $cf->format(1234.56, "ESP");
 * // $formattedAmount is now == "1.234,56 Ptas"
 *
 * $formattedAmount = $cf->format(1234.56, "GBP");
 * // $formattedAmount is now == "&pound;1.234,56"
 *
 * $decimal = $cf->toDecimal("1.234,56 Ptas");
 * // $decimal is now == 1234.56;
 *
 * $isValid = $cf->validate("1.234,56 Ptas", "ESP");
 * // $isValid is now == true
 * $isValid = $cf->validate("1.234,56", "ESP");
 * // $isValid is now == true
 * $isValid = $cf->validate("1,234.56 Ptas", "ESP");
 * // $isValid is now == false
 *
 * -------------------------------------------------------------------------
 *
 * @author    Simon Wade <simonvwade@yahoo.com>
 * @version $id: CurrencyFormatter.inc,v 1.1.1.1 2001/08/13 07:48:35 simonw Exp $
 * @access    public
 * @package    currency
 */
class CurrencyFormatter
{

    /**
     * A list of all the supported currency formatting styles.
     */
    var $formattingStyles = array(
        "standard",
        "noDecimals",
        "dotThousandsCommaDecimal",
        "apostropheThousandsDotDecimal",
        "apostropheThousandsNoDecimals",
        "spaceThousandsDotDecimal",
        "spaceThousandsCommaDecimal",
        "indian"
    );

    /**
     * A hashtable that maps the supported ISO 4217 currency codes to the formatting
     * style to be used for amounts in that currency.
     */
    var $ISOCodeStyles = array(
        "ARS" => "dotThousandsCommaDecimal",
        "AUD" => "standard",
        "ATS" => "dotThousandsCommaDecimal",
        "BEF" => "dotThousandsCommaDecimal",
        "REAL" => "dotThousandsCommaDecimal",
        "BRL" => "dotThousandsCommaDecimal",
        "CAD" => "standard",
        "COL" => "standard",
        "DKR" => "dotThousandsCommaDecimal",
        "FIM" => "spaceThousandsCommaDecimal",
        "FRF" => "spaceThousandsCommaDecimal",
        "DEM" => "dotThousandsCommaDecimal",
        "GRD" => "dotThousandsCommaDecimal",
        "HKD" => "standard",
        "IEP" => "standard",
        "ISK" => "dotThousandsCommaDecimal",
        "INR" => "indian",
        "ITL" => "dotThousandsCommaDecimal",
        "YPY" => "noDecimals",
        "LTL" => "dotThousandsCommaDecimal",
        "MXP" => "standard",
        "NLG" => "dotThousandsCommaDecimal",
        "NZD" => "standard",
        "NOK" => "dotThousandsCommaDecimal",
        "SGD" => "standard",
        "ZAR" => "standard",
        "KRW" => "noDecimals",
        "ESP" => "dotThousandsCommaDecimal",
        "SEK" => "spaceThousandsDotDecimal",
        "CHF" => "apostropheThousandsDotDecimal",
        "THB" => "standard",
        "GBP" => "standard",
        "USD" => "standard",
        "CZK" => "dotThousandsCommaDecimal",
        "HUF" => "standard",
        "LUF" => "apostropheThousandsDotDecimal",
        "PLZ" => "spaceThousandsCommaDecimal",
        "PTE" => "dotThousandsCommaDecimal",
        "TRL" => "standard"
    );

    /**
     * A list of all the supported ISO 4217 currency codes that don't have a dollar
     * sign as their symbol.
     */
    var $nonDollarSymbols = array(
        "ATS" => "&Ouml;S ",
        "BEF" => "BEF ",
        "REAL" => "R\$",
        "BRL" => "R\$",
        "DKR" => " kr",
        "FIM" => " mk",
        "FRF" => " F",
        "DEM" => "DM",
        "GRD" => " GRD",
        "HKD" => "HK\$",
        "IEP" => "IR&pound;",
        "ISK" => " kr",
        "INR" => "Rs.",
        "ITL" => "L. ",
        "YPY" => "&yen;",
        "LTL" => " Lt",
        "NLG" => "f ",
        "NOK" => " kr",
        "ZAR" => "R",
        "KRW" => "\\",
        "ESP" => " Ptas",
        "SEK" => " kr",
        "CHF" => "SFr ",
        "THB" => " Bt",
        "GBP" => "&pound;",
        "CZK" => " Kc",
        "HUF" => "HK\$",
        "LUF" => " F",
        "PLZ" => " zl",
        "PTE" => " Esc",
        "TRL" => "TL"
    );

    /**
     * A list of the ISO 4217 currency codes that have their symbol displayed
     * after the amount.
     */
    var $currenciesWithSymbolsAfterAmount = array(
        "DKR", "FIM", "FRF", "DEM", "GRD", "ISK", "ITL", "LTL",
        "NOK", "ESP", "SEK", "THB", "CZK", "LUF", "PLZ", "PTE",
        "TRL"
    );

    /**
     * Sets up the instance variables. Must be called for the object to operate correctly.
     * @access public
     */
    function CurrencyFormatter()
    {
        //* debug */ echo "function CurrencyFormatter()<br>\n";
    }

    /**
     * Returns the specified amount formatted according to the formatting style
     * used for the specified ISO 4217 currency code.
     *
     * @access public
     * @param double $amount The amount to be formatted.
     * @param string $ISOCode ISO 4217 currency code.
     * @return string The formatted amount.
     */
    function format($amount, $ISOCode)
    {
        //* debug */ echo "function format( $amount, $ISOCode )<br>\n";
        $style = $this->getStyleForISOCode($ISOCode);
        return $this->formatWithStyle($amount, $style);
    }

    /**
     * Returns the specified amount formatted according to the formatting style
     * used for the specified ISO 4217 currency code with the currency symbol
     * included.
     */
    function formatWithSymbol($amount, $ISOCode)
    {
        //* debug */ echo "function formatWithSymbol( $amount, $ISOCode )<br>\n";
        $amount = $this->format($amount, $ISOCode);
        list($prefix, $suffix) = $this->getPrefixSuffixArray($ISOCode);
        return $prefix . $amount . $suffix;
    }

    /**
     * Validates whether the specified amount is properly formatted
     * according to the formatting style used for the specified ISO
     * 4217 currency code. Returns true if the specified amount is
     * properly formatted.
     *
     * @access public
     * @param double $amount The amount to be validated.
     * @param string $ISOCode ISO 4217 currency code.
     * @return boolean True if $amount is properly formatted, false if not.
     */
    function validate($amount, $ISOCode)
    {
        //* debug */ echo "function validate( $amount, $ISOCode )<br>\n";
        $amount = $this->removePrefixAndSuffix($amount, $ISOCode);
        $style = $this->getStyleForISOCode($ISOCode);
        return $this->validateForStyle($amount, $style);
    }

    /**
     * Returns the decimal amount represented by the amount
     * according to the formatting style used for the specified ISO
     * 4217 currency code.
     *
     * @access public
     * @param double $amount The amount to be validated.
     * @param string $ISOCode ISO 4217 currency code.
     * @return double
     */
    function toDecimal($amount, $ISOCode)
    {
        //* debug */ echo "function toDecimal( $amount, $ISOCode )<br>\n";
        $amount = $this->removePrefixAndSuffix($amount, $ISOCode);
        $style = $this->getStyleForISOCode($ISOCode);
        return $this->toDecimalForStyle($amount, $style);
    }

    /**
     * Returns the decimal amount represented by the amount according to the
     * specified formatting style.
     *
     * @access public
     * @param double $amount The amount to be validated.
     * @param string $style The formatting style to be used.
     * @return double
     */
    function toDecimalForStyle($amount, $style)
    {
        //* debug */ echo "function toDecimalForStyle( $amount, $style )<br>\n";
        list($thousandsSeparator, $decimalPlace) = $this->getSeparators($style);
        $rv = preg_replace("/{$thousandsSeparator}/", "", $amount);
        $rv = preg_replace("/{$decimalPlace}/", ".", $rv);
        $rv = $rv + 0;
        return $rv;
    }

    /**
     * Checks to see if the formatting for the specified code is supported.
     *
     * @param string $ISOCode ISO 4217 currency code.
     * @access public
     * @return boolean
     */
    function supportsISOCode($ISOCode)
    {
        //* debug */ echo "function supportsISOCode( $ISOCode )<br>\n";
        if (isset($this->ISOCodeStyles[strtoupper($ISOCode)])) {
            return true;
        }
    }

    /**
     * Returns an hashtable of the supported formatting styles.
     * @access public
     * @return array
     */
    function getFormattingStyles()
    {
        //* debug */ echo "function getFormattingStyles()<br>\n";
        return $this->formattingStyles;
    }

    /**
     * Returns an array of the supported ISO 4217 codes.
     * @access public
     * @return array
     */
    function getISOCodes()
    {
        //* debug */ echo "function getISOCodes()<br>\n";
        $codes = array();
        while (list($key, $val) = each($this->ISOCodeStyles)) {
            $codes[] = $key;
        }
        reset($this->ISOCodeStyles);

        return $codes;
    }

    /**
     * Returns the key of the formatting style used for the specified ISO 4217
     * currency code.
     *
     * @access public
     * @param string $ISOCode ISO 4217 currency code.
     * @return string
     */
    function getStyleForISOCode($ISOCode)
    {
        //* debug */ echo "function getStyleForISOCode( $ISOCode )<br>\n";
        return $this->ISOCodeStyles[strtoupper($ISOCode)];
    }

    /**
     * Returns the symbol (ie. '$' or 'Y') for the specified ISO
     * 4217 currency code.
     *
     * @access public
     * @param string $ISOCode ISO 4217 currency code.
     * @return string
     */
    function getSymbol($ISOCode)
    {
        //* debug */ echo "function getSymbol( $ISOCode )<br>\n";
        //* debug */ echo "function getSymbol( $ISOCode )".NL;
        if (!$symbol = $this->nonDollarSymbols[strtoupper($ISOCode)]) {
            $symbol = "\$";
        }

        return $symbol;
    }

    /**
     * Returns true if the symbol for the specified ISO 4217 currency code
     * should be displayed after (to the right of) the amount.
     *
     * @access public
     * @param string $ISOCode ISO 4217 currency code.
     * @return boolean
     */
    function symbolIsAfterAmount($ISOCode)
    {
        //* debug */ echo "function symbolIsAfterAmount( $ISOCode )<br>\n";
        $ISOCode = strtoupper($ISOCode);
        for ($i = 0; $i < sizeof($this->currenciesWithSymbolsAfterAmount); $i++) {
            if ($this->currenciesWithSymbolsAfterAmount[$i] == $ISOCode) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convenience function that returns an array with two elements: a prefix
     * and a suffix.
     * eg.
     *        list($prefix, $suffix) = $cf->getPrefixSuffixArray( "AUD" );
     *        echo $prefix.$cf->format( 9999.95, "AUD" ).$suffix; // outputs "$9,999.95"*
     *
     * @access public
     * @param string $ISOCode ISO 4217 currency code.
     * @return array
     */
    function getPrefixSuffixArray($ISOCode)
    {
        //* debug */ echo "function getPrefixSuffixArray( $ISOCode )<br>\n";
        $prefix = $suffix = "";

        $symbol = $this->getSymbol($ISOCode);
        if ($this->symbolIsAfterAmount($ISOCode)) {
            $suffix = $symbol;
        } else {
            $prefix = $symbol;
        }
        return array($prefix, $suffix);
    }

    /**
     * Removes the prefix and suffix from an $amount formatted for the specified $ISOCode.
     * @access public
     * @param double $amount
     * @param string $ISOCode
     * @return string
     */
    function removePrefixAndSuffix($amount, $ISOCode)
    {
        //* debug */ echo "function removePrefixAndSuffix( $amount, $ISOCode )".NL;
        list($prefix, $suffix) = $this->getPrefixSuffixArray($ISOCode);
        $amount = preg_replace("/^" . preg_quote($prefix) . "/", "", $amount);
        $amount = preg_replace("/" . preg_quote($suffix) . "$/", "", $amount);
        return $amount;
    }

    /**
     * Returns a two element array that contains the thousands separator and the
     * the decimal place separator respectively for the specified formatting style.
     *
     * @access private
     * @param string $style The formatting style to be used.
     * @return array
     */
    function getSeparators($style)
    {
        //* debug */ echo "function getSeparators( $style )<br>\n";
        switch ($style) {
            case "dotThousandsCommaDecimal" :
                $thousandsSeparator = "\.";
                $decimalPlace = ",";
                break;
            case "dotThousandsNoDecimals" :
                $thousandsSeparator = "\.";
                $decimalPlace = ",";
                break;
            case "spaceThousandsCommaDecimal" :
                $thousandsSeparator = " ";
                $decimalPlace = ",";
                break;
            case "indian" :
                $thousandsSeparator = ",";
                $decimalPlace = "\.";
                break;
            case "noDecimals" :
                $thousandsSeparator = ",";
                $decimalPlace = "\.";
                break;
            case "spaceThousandsDotDecimal" :
                $thousandsSeparator = " ";
                $decimalPlace = "\.";
                break;
            default :
                $thousandsSeparator = ",";
                $decimalPlace = "\.";
                break;
        }

        return array($thousandsSeparator, $decimalPlace);
    }

    /**
     * Validates whether the specified amount is properly formatted
     * according to the specified formatting style. Returns true if
     * the specified amount is properly formatted.
     *
     * @access public
     * @param double $amount The amount to be validated.
     * @param string $style The formatting style to validate.
     * @return boolean
     */
    function validateForStyle($amount, $style)
    {
        //* debug */ echo "function validateForStyle( $amount, $style )<br>\n";
        list($thousandsSeparator, $decimalPlace) = $this->getSeparators($style);
        $regExp = "/([0-9]{1,3}" . $thousandsSeparator . ")*[0-9]*"
            . "(" . $decimalPlace . "[0-9]+){0,1}/";
        $rv = preg_match($regExp, $amount);

        return $rv;
    }

    /**
     * Returns the specified amount formatted according to the specified formatting
     * style.
     *
     * @access public
     * @param double $amount The amount to be formatted.
     * @param string $style The style to be used for formatting.
     * @return string
     */
    function formatWithStyle($amount, $style)
    {
        //* debug */ echo "function formatWithStyle( $amount, $style )<br>\n";
        settype($amount, "double");
        switch ($style) {
            case "dotThousandsCommaDecimal" :
                $rv = number_format($amount, 2, ",", ".");
                break;
            case "dotThousandsNoDecimals" :
                $str = number_format($amount, 2, "", ".");
                $rv = substr($str, 0, -3);
                break;
            case "spaceThousandsCommaDecimal" :
                $rv = number_format($amount, 2, ",", " ");
                break;
            case "indian" :
                list($digits, $decimals) = explode(".", $amount);
                if (($len = strlen($digits)) >= 5) {
                    $bit = substr($digits, 0, $len - 3) / 100;
                    $rv = number_format($bit, 2, ",", ",")
                        . "," . substr($digits, $len - 3)
                        . ".$decimals";
                } else
                    $rv = number_format($amount, 2);
                break;
            case "noDecimals" :
                $str = number_format($amount, 2, "", ",");
                $rv = substr($str, 0, -3);
                break;
            case "spaceThousandsDotDecimal" :
                $rv = number_format($amount, 2, ".", " ");
                break;
            case "apostropheThousandsNoDecimals" :
                $rv = number_format($amount, 2, ".", " ");
                $rv = substr($str, 0, -3);
                break;
            case "apostropheThousandsDotDecimal" :
                $rv = number_format($amount, 2, ".", "'");
                break;
            default :
                $rv = number_format($amount, 2);
                break;
        }
        return $rv;
    }

}

