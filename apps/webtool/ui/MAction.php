<?php

/* Copyright [2011, 2012, 2013] da Universidade Federal de Juiz de Fora
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

class MAction {

    /*
     * Modifiers:
     * :action|element|update   AJAX (ação|elemento_base|elemento a ser atualizado)
     * *action|element|callback AJAX_JSON (ação|elemento_base|função callback)
     * @action                  POST action via ajax
     * >action|target           GET action via ajax (com target opcional)
     * !code                    javascript:codigo
     * +action                  GET action em nova janela do browser
     * ^action                  GET action em um dialog
     * =action                  Redirect para action
     * dialog:id                Exibe o dialog indicado por div#id
     * prompt:id                Exibe o prompt indicado por div#id
     * help:id                  Abre o dialogo de help indicado por div#id
     * file:url                 Download de arquivo via AJAX usando plugin
     * POST                     POST do formulário atual
     * SUBMIT                   POST na URL corrente
     * PRINT, PDF, REPORT 	Abre nova janela do browser
     */
    
    private static $modifiers = ">@!:+^=%*";

    public static function isAction($string) {
        return  ($string != '') && 
            ((strpos(self::$modifiers, $string{0}) !== false) || 
                preg_match("/^(SUBMIT|PRINT|PDF|FILE|REPORT|POST|OPEN(.*)|CLOSE|PROMPT(.*)|DIALOG(.*)|HELP(.*)|FILE(.*))$/", strtoupper($string)));
    }

    private static function getHrefAction($href) {
        $app = Manager::getApp();
        $re = '#^(\/?)' . $app . '\/#';
        if (preg_match($re, $href)) {
            $href = preg_replace($re, '', $href);
        }
        return Manager::getURL($href);
    }

    public static function getHref($href) {
        if ($href != '') {
            if ($href{0} == '#') {
                $href = Manager::getStaticURL(Manager::getApp(), substr($href, 1));
            } else {
                $href = MAction::getHrefAction($href);
            }
        }
        return $href;
    }

    private static function getAction($action) {
        $app = Manager::getApp();
        $re = '#^(\/?)' . $app . '\/#';
        if (preg_match($re, $action)) {
            $action = preg_replace($re, '', $action);
        }
        return $app . '/' . $action;
    }
    
    public static function parseAction($action) {
        if ($action == '') {
            return $action;
        }
        $upper = strtoupper($action);
        $modifier = $action{0};
        if (strpos(self::$modifiers, $modifier) !== false) {
            if ($modifier == '!') {
                return $action;
            }
            $goto = self::getAction(substr($action, 1));
            return $modifier . $goto;
        } elseif ($upper == 'POST') {
            return "POST";
        } elseif (substr($upper, 0, 6) == 'PROMPT') {
            if (strpos($action, ':') !== false) {
                list($action, $id) = explode(':', $action);
                return "p#" . $id;
            }
            return '';
        } elseif (substr($upper, 0, 4) == 'HELP') {
            if (strpos($action, ':') !== false) {
                list($action, $id) = explode(':', $action);
                return "h#" . $id;
            }
            return '';
        } elseif (substr($upper, 0, 6) == 'DIALOG') {
            if (strpos($action, ':') !== false) {
                list($action, $id) = explode(':', $action);
                return "d#" . $id;
            }
            return '';
        } elseif (substr($upper, 0, 4) == 'FILE') {
            if (strpos($action, ':') !== false) {
                list($action, $url) = explode(':', $action);
                return "f#" . self::getAction($url);
            }
            return '';
        } else {
            return $action;
        }
    }

    public static function generate($control, $id = "") {
        if ($control->action != "") {
            $action = $control->action;
            $id = $id ?: $control->id;
            if (self::isAction($action)) {
                // a associação de uma action com o event javascript correspondente
                // é feita no cliente (plugin jquery.manager.action)
                // o cliente espera a action no formato:  {modifier}app[/module]/controller/action
                $parsedAction = self::parseAction($action);
                if ($parsedAction != "") {
                    $control->property->manager['action'] = $parsedAction;
                    $control->addClass('maction');
                }
            } else {
                $href = self::getHref($action);
                $control->property->href = $href;
            }
        }
    }

}

?>