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
 * Adaptado a partir do trabalho de Johannes Buchner
 * http://johbuc6.coconia.net/doku.php/mediawiki2html_machine
 */
class MWiki
{

    function getPartBetween($str, $a, $b)
    {
        $start = strpos($str, $a) + strlen($a);
        if (strpos($str, $a) === false) {
            return false;
        }
        $length = strpos($str, $b, $start) - $start;
        if (strpos($str, $b, $start) === false) {
            return false;
        }
        return substr($str, $start, $length);
    }

    function simpleText($html)
    {
        $html = str_replace('&ndash;', '-', $html);
        $html = str_replace('&quot;', '"', $html);
        $html = preg_replace('/\&amp;(nbsp);/', '&${1};', $html);
        //formatting
        // pre/code - using geshi
        $html = preg_replace_callback("/\s*<(math|pre|code|nowiki)(?:lang=[\"']([\w-]+)[\"']|line=[\"'](\d*)[\"']|escaped=[\"'](true|false)?[\"']|highlight=[\"']((?:\d+[,-])*\d+)[\"']|\s)+>(.*)<\/(math|pre|code|nowiki)>\s*/siU", 'self::helper_pre', $html);
        // bold
        $html = preg_replace('/\'\'\'([^\n\']+)\'\'\'/', '<strong>${1}</strong>', $html);
        // emphasized
        $html = preg_replace('/\'\'([^\'\n]+)\'\'?/', '<em>${1}</em>', $html);
        // image
        $html = preg_replace_callback('/\[\[Image:([^\]]+)\]\]/', 'self::helper_image', $html);
        // anchor
        $html = preg_replace_callback('/\[\[Anchor:([^\]]+)\]\]/', 'self::helper_anchor', $html);
        //interwiki links
        $html = preg_replace_callback('/\[\[([^\|\n\]:]+)[\|]([^\]]+)\]\]/', 'self::helper_interwikilinks', $html);
        // without text
        $html = preg_replace_callback('/\[\[([^\|\n\]:]+)\]\]/', 'self::helper_interwikilinks', $html);
        // interwiki
//        $html = preg_replace('/{{([^\|\n\}]+)([\|]?([^\}]+))+\}\}/', 'Interwiki: ${1} &raquo; ${3}', $html);
        // categories
//        $html = preg_replace('/\[\[([^\|\n\]]{2})([\:]([^\]]+))?\]\]/', 'Translation: ${1} &raquo; ${3}', $html);
//        $html = preg_replace('/\[\[([^\|\n\]]+)([\:]([^\]]+))?\]\]/', 'Category: ${1} - ${2}', $html);
        //links
        $html = preg_replace_callback('/\[([^\[\]\|\n\': ]+)\]/', 'self::helper_externlinks', $html);
        // with text and target
        $html = preg_replace_callback('/\[([^\[\]\|\n\' ]+)[\| ]([^\]\']+)[\| ]([^\]\']+)\]/', 'self::helper_externlinks', $html);
        // with target only
        $html = preg_replace_callback('/\[([^\[\]\|\n\' ]+)[\| ]([^\]\']+)\]/', 'self::helper_externlinks', $html);
        // allowed tags
        $html = preg_replace('/&lt;(\/?)(small|sup|sub|u)&gt;/', '<${1}${2}>', $html);
        $html = preg_replace('/\n*&lt;br *\/?&gt;\n*/', "\n", $html);
        $html = preg_replace('/&lt;!--/', '<!--', $html);
        $html = preg_replace('/--&gt;/', ' -->', $html);
        //lists
        $html = preg_replace('/(\n[ ]*[^#* ][^\n]*)\n(([ ]*[*]([^\n]*)\n)+)/', '${1}<ul>' . "\n" . '${2}' . '</ul>' . "\n", $html);
        $html = preg_replace('/(\n[ ]*[^#* ][^\n]*)\n(([ ]*[#]([^\n]*)\n)+)/', '${1}<ol>' . "\n" . '${2}' . '</ol>' . "\n", $html);
        $html = preg_replace('/\n[ ]*[\*#]+([^\n]*)/', '<li>${1}</li>', $html);
        $html = preg_replace('/----/', '<hr />', $html);
        // headings
        for ($i = 7; $i > 0; $i--) {
            $html = preg_replace('/\n+[=]{' . $i . '}([^=]+)[=]{' . $i . '}[\n\r]*/', '<h' . $i . '>${1}</h' . $i . '>', $html);
        }
        // line breaks
        $html = preg_replace('/[\n\r]{4}/', "<br/><br/>", $html);
        $html = preg_replace('/[\n\r]{2}/', "<br/>", $html);
        $html = preg_replace('/[>]<br\/>[<]/', "><", $html);

        return $html;
    }

    function parse($title, $page)
    {
        $text = ($this->getPartBetween($page, '<text xml:space="preserve">', '</text>'));
        $html = $text;
        $html = html_entity_decode($html);
        $html = str_replace('&ndash;', '-', $html);
        $html = str_replace('&quot;', '"', $html);
        $html = preg_replace('/\&amp;(nbsp);/', '&${1};', $html);
        $html = str_replace('{{PAGENAME}}', $title, $html);
        // Table
        $html = $this->convertTables($html);
        $html = $this->simpleText($html);
        $output = "\n<div class=\"mWiki\">";
        $output .= $html;
        $output .= "</div>\n";
        return $output;
    }

    function giveSource($page)
    {
        $text = ($this->getPartBetween($page, '<text xml:space="preserve">', '</text>'));
        $text = "<pre>" . $text . "</pre>";
        return $text;
    }

    function helper_anchor($matches)
    {
        $name = $matches[1];
        return '<a name="' . $name . '"></a>';
    }

    function helper_externlinks($matches)
    {
        $href = $matches[1];
        if (substr(strtoupper($href), 0, 4) == 'HTTP') {
            $text = empty($matches[2]) ? $matches[1] : $matches[2];
            $target = $matches[3] ?: '';
            return '<a class="mWikiLink" href="' . $href . '" target="' . $target . '" >' . $text . '</a>';
        } else {
            return '[' . $href . ']';
        }
    }

    function helper_interwikilinks($matches)
    {
        $action = $matches[1];
        $text = empty($matches[2]) ? $matches[1] : $matches[2];
        $target = $matches[3];
        if (substr($action, 0, 1) == '#') {
            $link = "<a class=\"mWikiLink\" href=\"{$action}\">{$text}</a>";
        } else {
            //$mlink = new MLink('', '', $action, $text, $target);
            //$link = $mlink->generate();
            $target = ($target ? "target=\"{$target}\"" : "");
            $link = "<a class=\"mWikiLink\" href=\"{$action}\" {$target}>{$text}</a>";
        }
        return $link;
    }

    function helper_pre($matches)
    {
        $language = strtolower(trim($matches[2]));
        $line = trim($matches[3]);
        $escaped = trim($matches[4]);

        $code = $this->code_trim($matches[6]);
        if ($escaped == "true")
            $code = htmlspecialchars_decode($code);

        //$syntax = new MSyntax('', $code, $language);
//        $geshi->enable_keyword_links(false);
        //START LINE HIGHLIGHT SUPPORT
        $highlight = array();
        if (!empty($matches[5])) {
            $highlight = strpos($matches[5], ',') == false ? array($matches[3]) : explode(',', $matches[3]);

            $h_lines = array();
            for ($i = 0; $i < sizeof($highlight); $i++) {
                $h_range = explode('-', $highlight[$i]);

                if (sizeof($h_range) == 2)
                    $h_lines = array_merge($h_lines, range($h_range[0], $h_range[1]));
                else
                    array_push($h_lines, $highlight[$i]);
            }

            //$geshi->highlight_lines_extra($h_lines);
        }
        //END LINE HIGHLIGHT SUPPORT

        $linenums = ($line ? ' linenums' : '');
        $output = "\n<div class=\"prettyprint{$linenums}\">";
        $output .= $code;
        $output .= "</div>";
        /*
        $output = "\n<div class=\"geshi\">";
        if ($line) {
            $output .= "<table><tr><td class=\"line_numbers\">";
            $output .= $this->line_numbers($code, $line);
            $output .= "</td><td class=\"code\">";
            $output .= $syntax->generate();
            $output .= "</td></tr></table>";
        } else {
            $output .= "<div class=\"code\">";
            $output .= $syntax->generate();
            $output .= "</div>";
        }
        $output .= "</div>";
        */
        return $output;
    }

    function code_trim($code)
    {
        // special ltrim b/c leading whitespace matters on 1st line of content
        $code = preg_replace("/^\s*\n/siU", "", $code);
        $code = rtrim($code);
        return $code;
    }

    function line_numbers($code, $start)
    {
        $line_count = count(explode("\n", $code));
        $output = "<pre>";
        for ($i = 0; $i < $line_count; $i++) {
            $output .= ($start + $i) . "\n";
        }
        $output .= "</pre>";
        return $output;
    }

    function helper_image($matches)
    {
        $m = explode('|', $matches[1]);
        $source = Manager::getStaticURL('', 'images/' . $m[0], true);
        $image = new MImage('', '', '', $source);
//        return '<a ' . $class . ' href="/' . $target . '">' . $text . '</a>';
        return $image->generate();
    }

    function convertTables($text)
    {
        $lines = explode("\n", $text);
        $innerTable = 0;
        $innerTableData = array();
        foreach ($lines as $line) {
            $line = str_replace("position:relative", "", $line);
            $line = str_replace("position:absolute", "", $line);
            if (substr($line, 0, 2) == '{|') {
                $innerTable++;
            }
            $innerTableData[$innerTable] .= $line . "\n";
            if ($innerTable) {
                // we're inside
                if (substr($line, 0, 2) == '|}') {
                    $innerTableConverted = $this->convertTable($innerTableData[$innerTable]);
                    $innerTableData[$innerTable] = "";
                    $innerTable--;
                    $innerTableData[$innerTable] .= $innerTableConverted . "\n";
                }
            }
        }
        $output = $innerTableData[0];
        return $output;
    }

    function convertTable($tableText)
    {
        $text = $tableText;
        $lines = explode("\n", $text);
        foreach ($lines as $line) {
            $line = trim($line);
            $firstChar = substr($line, 0, 1);
            $secondChar = substr($line, 1, 1);
            if ($firstChar == '{') {
                //begin of the table
                $stuff = explode('| ', substr($line, 1), 2);
                $tableOpen = true;
                $table = "<table " . $stuff[1] . ">\n";
            } elseif ($firstChar == '|') {
                // table related
                $line = substr($line, 1);
                $secondChar = substr($line, 0, 1);
                if ($secondChar == '-') {
                    // row break
                    if ($thOpen) {
                        $table .= "</th>\n";
                    }
                    if ($tdOpen) {
                        $table .= "</td>\n";
                    }
                    if ($rowOpen) {
                        $table .= "\t</tr>\n";
                    }
                    $table .= "\t<tr>\n";
                    $rowOpen = true;
                    $tdOpen = false;
                    $thOpen = false;
                } else if ($secondChar == '}') {
                    // table end
                    break;
                } else {
                    // td
                    $columns = explode('||', $line);
                    foreach ($columns as $column) {
                        $stuff = explode('| ', $column, 2);
                        if ($tdOpen) {
                            $table .= "</td>\n";
                        }
                        if (count($stuff) == 1) {
                            $table .= "\t\t<td>" . $this->simpleText($stuff[0]);
                        } else {
                            $table .= "\t\t<td " . $stuff[0] . ">" . $this->simpleText($stuff[1]);
                        }
                        $tdOpen = true;
                    }
                }
            } elseif ($firstChar == '!') {
                // th
                $line = substr($line, 1);
                $columns = explode('!!', $line);
                foreach ($columns as $column) {
                    $stuff = explode('! ', $column, 2);
                    if ($thOpen) {
                        $table .= "</th>\n";
                    }
                    if (count($stuff) == 1) {
                        $table .= "\t\t<th>" . $this->simpleText($stuff[0]);
                    } else {
                        $table .= "\t\t<th " . $stuff[0] . ">" . $this->simpleText($stuff[1]);
                    }
                    $thOpen = true;
                }
            } else {
                // plain text
                $table .= $this->simpleText($line) . "\n";
            }
        }
        if ($thOpen) {
            $table .= "</th>\n";
        }
        if ($tdOpen) {
            $table .= "</td>\n";
        }
        if ($rowOpen) {
            $table .= "\t</tr>\n";
        }
        if ($tableOpen) {
            $table .= "</table>\n";
        }
        return $table;
    }

}
