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

class MState
{

    private $variables;
    public $viewState = '';
    private $id;

    public function __construct($id)
    {
        $this->id = $id . '__VIEWSTATE';
        $this->variables = array();
        $this->viewState = '';
    }

    public function set($var, $value, $componentName = '')
    {
        if (!$component_name) {
            $this->variables[$var] = $value;
        } else {
            $this->variables[$componentName][$var] = $value;
        }
    }

    public function get($var, $componentName = '')
    {

        if (!$component_name) {
            return $this->variables[$var];
        } else {
            return $this->variables[$componentName][$var];
        }
    }

    public function loadViewState()
    {
        $this->viewState = mrequest($this->id);

        if ($this->viewState) {
            $s = base64_decode($this->viewState);
            $this->variables = unserialize($s);
        }
    }

    public function saveViewState()
    {
        if ($this->variables) {
            $s = serialize($this->variables);
            $this->viewState = base64_encode($s);
        }
    }

    public function setViewState($value)
    {
        $this->viewState = $value;
    }

    public function getViewState()
    {
        return $this->viewState;
    }

    public function getVariables()
    {
        return $this->variables;
    }

    public function getCode()
    {
        $this->saveViewState();
        return $this->viewState;
    }

    public function getId()
    {
        return $this->id;
    }

}
