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
 *  Adapted for Maestro
 *  IAM_CSVDump A class form performing a query dump and sending it to the browser or setting it or download.
 * @package    iam_csvdump
 */

/**
 *  IAM_CSVDump A class form performing a query dump and sending it to the browser or setting it or download.
 * @author     IvÃ¡n Ariel Melgrati <phpclasses@imelgrat.mailshell.com>
 * @package    iam_csvdump
 * @version 1.0
 *
 *  IAM_CSVDump A class form performing a query dump and sending it to the browser or setting it or download.
 *
 *  Browser and OS detection for appropriate handling of download and EOL chars.
 *
 *  Requires PHP v 4.0+ and MySQL 3.23+. Some portions taken from the CSV_UTIL_CLASS by Andrej Arn <andrej@blueshoes.org>.
 *
 *  This library is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2 of the License, or (at your option) any later version.
 *
 *  This library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  Lesser General Public License for more details.
 */
class MCSVDump
{

    private $separator;
    private $eol;

    const WINDOWS_EOL = "\r\n";
    const MAC_EOL = "\r";
    const LINUX_EOL = "\n";

    public function __construct($separator = '', $eol = '')
    {
        $this->separator = ($separator ?: Manager::getOptions('csv'));
        $this->eol = ($eol ?: $this->_define_newline());
    }

    /**
     * @desc Takes an array and creates a csv string from it.
     *
     * @access public
     * @param  Array $array (see below)
     * @param  String $separator Field separator ()default is ';')
     * @param  String $trim If the cells should be trimmed , default is 'both'. It can also be 'left', 'right' or 'both'. 'none' makes it faster since omits many function calls.
     * @param  Boolean $removeEmptyLines (default is TRUE. removes "lines" that have no value, would come out empty.)
     * @return String A CSV String. It returns an empty string if there Array is empty (NULL)
     * @todo Add param "fill to fit max length"?
     */
    public function arrayToCsvString($array, $separator = ',', $trim = 'both', $removeEmptyLines = TRUE)
    {
        $separator = $this->separator;
        if (!is_array($array) || empty($array))
            return '';

        switch ($trim) {
            case 'none':
                $trimFunction = FALSE;
                break;
            case 'left':
                $trimFunction = 'ltrim';
                break;
            case 'right':
                $trimFunction = 'rtrim';
                break;
            default: //'both':
                $trimFunction = 'trim';
                break;
        }
        $ret = array();
        reset($array);
        if (is_array(current($array))) {
            while (list(, $lineArr) = each($array)) {
                if (!is_array($lineArr)) {
                    //Could issue a warning ...
                    $ret[] = array();
                } else {
                    $subArr = array();
                    while (list(, $val) = each($lineArr)) {
                        $val = $this->_valToCsvHelper($val, $separator, $trimFunction);
                        $subArr[] = $val;
                    }
                }
                $ret[] = join($separator, $subArr);
            }
            return join("\n", $ret);
        } else {
            while (list(, $val) = each($array)) {
                $val = $this->_valToCsvHelper($val, $separator, $trimFunction);
                $ret[] = $val;
            }
            return join($separator, $ret);
        }
    }

    /**
     * @desc Works on a string to include in a csv string.
     * @access private
     * @param  String $val
     * @param  String $separator
     * @param  Mixed $trimFunction If the cells should be trimmed , default is 'both'. It can also be 'left', 'right' or 'both'. 'none' makes it faster since omits many function calls.
     * @return String
     * @see    arrayToCsvString()
     */
    public function _valToCsvHelper($val, $separator, $trimFunction)
    {
        if ($trimFunction)
            $val = $trimFunction($val);
        //If there is a separator (;) or a quote (") or a linebreak in the string, we need to quote it.
        $needQuote = FALSE;
        do {
            if (strpos($val, '"') !== FALSE) {
                $val = str_replace('"', '""', $val);
                $needQuote = TRUE;
                break;
            }
            if (strpos($val, $separator) !== FALSE) {
                $needQuote = TRUE;
                break;
            }
            if ((strpos($val, "\n") !== FALSE) || (strpos($val, "\r") !== FALSE)) { // \r is for mac
                $needQuote = TRUE;
                break;
            }
        } while (FALSE);
        if ($needQuote) {
            $val = '"' . $val . '"';
        }
        return $val;
    }

    /**
     * @desc Define EOL character according to target OS
     * @access private
     * @return String A String containing the End Of Line Sequence corresponding to the client's OS
     */
    public function _define_newline()
    {
        $unewline = "\r\n";

        if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'win')) {
            $unewline = self::WINDOWS_EOL;
        } else if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'mac')) {
            $unewline = self::MAC_EOL;
        } else {
            $unewline = self::LINUX_EOL;
        }

        return $unewline;
    }

    /**
     * @desc Define the client's browser type
     * @access private
     * @return String A String containing the Browser's type or brand
     */
    public function _get_browser_type()
    {
        $USER_BROWSER_AGENT = "";

        if (ereg('OPERA(/| )([0-9].[0-9]{1,2})', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
            $USER_BROWSER_AGENT = 'OPERA';
        } else if (ereg('MSIE ([0-9].[0-9]{1,2})', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
            $USER_BROWSER_AGENT = 'IE';
        } else if (ereg('OMNIWEB/([0-9].[0-9]{1,2})', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
            $USER_BROWSER_AGENT = 'OMNIWEB';
        } else if (ereg('MOZILLA/([0-9].[0-9]{1,2})', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
            $USER_BROWSER_AGENT = 'MOZILLA';
        } else if (ereg('KONQUEROR/([0-9].[0-9]{1,2})', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
            $USER_BROWSER_AGENT = 'KONQUEROR';
        } else {
            $USER_BROWSER_AGENT = 'OTHER';
        }

        return $USER_BROWSER_AGENT;
    }

    /**
     * @desc Define MIME-TYPE according to target Browser
     * @access private
     * @return String A string containing the MIME-TYPE String corresponding to the client's browser
     */
    public function _get_mime_type()
    {
        $USER_BROWSER_AGENT = $this->_get_browser_type();

        $mime_type = ($USER_BROWSER_AGENT == 'IE' || $USER_BROWSER_AGENT == 'OPERA') ? 'application/octetstream' : 'application/octet-stream';
        return $mime_type;
    }

    /**
     * @desc Generates a CSV File from an SQL String (and outputs it to the browser)
     * @access private
     * @param  String $query_string An SQL statement (usually a SELECT statement)
     * @param  String $dbname Name of the Database
     * @param  String $user User to Access the Database
     * @param  String $password Password to Access the Database
     * @param  String $host Name of the Host holding the DB
     */
    public function _generate_csv($array)
    {
        $file = "";
        $crlf = $this->eol;
        foreach ($array as $str) {
            $file .= $this->arrayToCsvString($str) . $crlf;
        }
        $this->_send_file($file);
    }

    public function _generate_txt($array)
    {
        $file = "";
        $crlf = $this->eol;
        foreach ($array as $str) {
            $file .= $str . $crlf;
        }
        $this->_send_file($file);
    }

    public function _send_file($file)
    {
        ob_end_clean();
        echo $file;
    }

    /**
     * @desc Generate the CSV File and send it to browser or download it as a file
     * @access public
     * @param String $query_string An SQL statement (usually a SELECT statement)
     * @param String $filename Filename to use when downloading the File. Default="dump". If set to "", the dump is displayed on the browser.
     * @param String $extension Extension to use when downloading the File. Default="csv"
     * @param  String $dbname Name of the Database to use
     * @param  String $user User to Access the Database
     * @param  String $password Password to Access the Database
     * @param  String $host Name of the Host holding the DB
     */
    public function dump($array, $filename = "dump", $ext = "csv")
    {
        $now = gmdate('D, d M Y H:i:s') . ' GMT';
        $USER_BROWSER_AGENT = $this->_get_browser_type();

        if ($filename != "") {
            header('Content-Type: ' . $this->_get_mime_type());
            header('Expires: ' . $now);
            if ($USER_BROWSER_AGENT == 'IE') {
                header('Content-Disposition: inline; filename="' . $filename . '.' . $ext . '"');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
            } else {
                header('Content-Disposition: attachment; filename="' . $filename . '.' . $ext . '"');
                header('Pragma: no-cache');
            }

            $this->_generate_csv($array);
        }
    }

    public function dumpTxt($array, $filename = "dump", $ext = "txt")
    {
        $now = gmdate('D, d M Y H:i:s') . ' GMT';
        $USER_BROWSER_AGENT = $this->_get_browser_type();

        if ($filename != "") {
            header('Content-Type: ' . $this->_get_mime_type());
            header('Expires: ' . $now);
            if ($USER_BROWSER_AGENT == 'IE') {
                header('Content-Disposition: inline; filename="' . $filename . '.' . $ext . '"');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
            } else {
                header('Content-Disposition: attachment; filename="' . $filename . '.' . $ext . '"');
                header('Pragma: no-cache');
            }
            $this->_generate_txt($array);
        }
    }

    public function save($array, $fileName = '', $utf8Decode = false, $trim = 'both')
    {
        $content = "";
        $crlf = $this->eol;
        if (!is_array($array)) {
            $array = array($array);
        }
        foreach ($array as $str) {
            $content .= $this->arrayToCsvString($str, '', $trim) . $crlf;
        }
        if ($fileName == '') {
            $id = uniqid(md5(uniqid("")));  // generate a unique id to avoid name conflicts
            $fileOutput = $id . ".csv"; // the report generated file
        }
        if ($utf8Decode) {
            $content = utf8_decode($content);
        }
        $file = MFile::file($content, false, $fileName);
        return $file;
    }

}
