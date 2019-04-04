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

function _M($msg, $params = NULL)
{
    return Manager::getMessage($msg, $params) ?: $msg;
}

function mdump($var, $tag = null)
{
    Manager::traceDump($var, false, 0, $tag);
}

function mtrace($var)
{
    Manager::trace(print_r($var, true));
}

function mtracestack()
{
    return Manager::tracestack();
}

function mrequest($vars, $from = 'ALL', $order = '')
{
    if (is_array($vars)) {
        foreach ($vars as $v) {
            $values[$v] = mrequest($v, $from);
        }
        return $values;
    } else {
        $value = NULL;
        // Seek in all scope?
        if ($from == 'ALL') {
            // search in REQUEST
            if (is_null($value)) {
                $value = isset($_REQUEST[$vars]) ? $_REQUEST[$vars] : NULL;
            }

            if (is_null($value)) {
                // Not found in REQUEST? try GET or POST
                // Order? Default is use the same order as defined in php.ini ("EGPCS")
                if (!isset($order)) {
                    $order = ini_get('variables_order');
                }

                if (strpos($order, 'G') < strpos($order, 'P')) {
                    $value = isset($_GET[$vars]) ? $_GET[$vars] : NULL;

                    // If not found, search in post
                    if (is_null($value)) {
                        $value = isset($_POST[$vars]) ? $_POST[$vars] : NULL;
                    }
                } else {
                    $value = isset($_POST[$vars]) ? $_POST[$vars] : NULL;

                    // If not found, search in get
                    if (is_null($value)) {
                        $value = isset($_GET[$vars]) ? $_GET[$vars] : NULL;
                    }
                }
            }

            // If we still didn't have the value
            // let's try in the global scope
            if ((is_null($value)) && ((strpos($vars, '[')) === false)) {
                $value = isset($_GLOBALS[$vars]) ? $_GLOBALS[$vars] : NULL;
            }

            // If we still didn't has the value
            // let's try in the session scope

            if (is_null($value)) {
                if ($vars) {
                    $value = isset($_SESSION[$vars]) ? $_SESSION[$vars] : NULL;
                }
            }
        } else if ($from == 'GET') {
            $value = isset($_GET[$vars]) ? $_GET[$vars] : NULL;
        } elseif ($from == 'POST') {
            $value = isset($_POST[$vars]) ? $_POST[$vars] : NULL;
        } elseif ($from == 'SESSION') {
            $value = isset($_SESSION[$vars]) ? $_SESSION[$vars] : NULL;
        } elseif ($from == 'REQUEST') {
            $value = isset($_REQUEST[$vars]) ? $_REQUEST[$vars] : NULL;
        }
        return $value;
    }
}

/**
 * Check for valid JSON string
 * @param $x
 * @return bool
 */
function is_json($x) {
    if (!is_string($x) || trim($x) === "") return false;
    return $x === 'null' || (
            // Maybe an empty string, array or object
            $x === '""' ||
            $x === '[]' ||
            $x === '{}' ||
            // Maybe an encoded JSON string
            $x[0] === '"' && substr($x, -1) === '"' ||
            // Maybe a numeric array
            $x[0] === '[' && substr($x, -1) === ']' ||
            // Maybe an associative array
            $x[0] === '{' && substr($x, -1) === '}'
        ) && json_decode($x) !== null;
}

function shutdown()
{
    $error = error_get_last();
    //if ($error) mdump($error);
    Manager::errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
    if ($error['type'] & (E_ALL & ~E_NOTICE & ~E_STRICT)) {
        if (Manager::isAjaxCall()) {
            $ajax = Manager::getAjax();
            $ob = ob_get_clean();
            if ($ajax->isEmpty()) {
                $ajax->setType('page');
                $ajax->setData($ob);
            }
            $result = $ajax->returnData();
            echo $result;
        }
    }
}
