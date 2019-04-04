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

class MMessages
{

    public $lang;
    public $file;
    public $msg = array();

    public function __construct($lang = '')
    {
        $this->lang = $lang;
        $this->file = 'messages.' . ($lang ? $lang . '.' : '') . 'php';
    }

    public function loadMessages()
    {
        $file = Manager::getFrameworkPath('conf/' . $this->file);
        $msg = file_exists($file) ? require($file) : array();
        $this->msg = array_merge($this->msg, $msg);
    }

    public function addMessages($dir, $file = '')
    {
        if ($file == '') {
            $msgFile = realpath($dir . '/' . $this->file);
        } else {
            $msgFile = realpath($dir . '/' . $file);
        }
        if (file_exists($msgFile)) {
            $msg = @include_once($msgFile);
            $this->msg = array_merge($this->msg, $msg ?: array());
        }
    }

    public function get($key, $parameters = array())
    {
        $msg = vsprintf($this->msg[$key], $parameters);
        return $msg;
    }

    public function set($key, $msg)
    {
        $this->msg[$key] = $msg;
    }

}

?>