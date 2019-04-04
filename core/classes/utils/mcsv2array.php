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

class mcsv2array
{

    var $csvfile;
    var $csvfilepath;
    var $array;

    function csv2table($csvpath = "")
    {
        if ($csvpath != "") {
            $this->csvfilepath = $csvpath;
        } else {
            $this->csvfilepath = "";
        }
    }


    function getArray($csvfile, $columnstoshow = '', $filter = '', $delimiter = ';')
    {
        $this->csvfile = $this->csvfilepath . $csvfile;
        $this->filter = $filter;

        if ($columnstoshow != "") {
            $this->columnstoshow = explode($delimiter, $columnstoshow);
        } else {
            $this->columnstoshow = "";
        }

        if (!file_exists($this->csvfile)) {
            echo "<br><center><b>Warning: File not found!</b><br></center>";
            return;
        }

        $isfilter = $this->get_filters($delimiter);
        $fp = fopen($this->csvfile, "r");
        $count = 0;
        while ($data = fgetcsv($fp, 0, $delimiter)) {
            if ($count != 0 && $isfilter) {
                // check to see if the data follows the filter specified.
                $val = true;
                $val = $this->check_filter($data);
                if (!$val) continue;
            }
            $num = count($data);
            for ($c = 0; $c < $num; $c++) {
                // check if this column should be displayed or not.
                if ($this->check_column($c)) {
                    $array[$count][$c] = $data[$c];
                }
            }
            $count++;
        }
        fclose($fp);
        return $array;
    }

    function get_filters($delimiter)
    {
        if (isset($this->filter) && is_array($this->filter)) {
            foreach ($this->filter as $fnum => $fval) {
                $fnum--;
                $list = explode($delimiter, $fval);
                $this->condition[$fnum] = trim($list[0]);
                $this->value[$fnum] = trim($list[1]);
            }
            return true;
        }
        return false;
    }

    function check_filter($data)
    {

        $ret = true;

        foreach ($this->filter as $f => $fnum) {
            $f--;
            $op = trim($this->condition[$f]);
            $val = trim($this->value[$f]);
            $d = trim($data[$f]);
            switch ($op) {
                case ">" :
                    if ($d <= $val) $ret = false;
                    break;
                case "<" :
                    if ($d >= $val) $ret = false;
                    break;
                case "=" :
                    if (is_string($d)) {
                        if (strcasecmp($d, $val) != 0) $ret = false;
                    } else {
                        if ($d != $val) $ret = false;
                    }
                    break;
                case "!=" :
                    if (is_string($d)) {
                        if (strcasecmp($d, $val) == 0)
                            $ret = false;
                    } else {
                        if ($d != $val) ;
                        else $ret = false;
                    }
                    break;
            }
        }
        return $ret;
    }

    function check_column($c)
    {
        if ($this->columnstoshow == "")
            return true;

        foreach ($this->columnstoshow as $cs) {
            $cs = trim($cs);
            if (($cs - 1) == $c) return true;
        }
        return false;
    }
}
