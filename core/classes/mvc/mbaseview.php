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

class MBaseView
{

    public $application;
    public $module;
    public $controller;
    public $viewFile;
    public $data;

    public function __construct($application = '', $module = '', $controller = '', $viewFile = '')
    {
        $this->application = $application;
        $this->module = $module;
        $this->controller = $controller;
        $this->viewFile = $viewFile;
    }

    public function init()
    {
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function setArgs($args)
    {
        if (count($args)) {
            foreach ($args as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    public function getPath()
    {
        return pathinfo($this->viewFile, PATHINFO_DIRNAME);
    }

    public function process($controller, $parameters)
    {
//        $this->setArgs($parameters);
        mtrace('view file = ' . $this->viewFile);
        $this->controller = $controller;
        $path = $this->getPath();
        Manager::addAutoloadPath($path);
        $extension = pathinfo($this->viewFile, PATHINFO_EXTENSION);
        $this->data = $parameters;
        $process = 'process' . $extension;
        $content = $this->$process();
        return $content;
    }

    protected function processPHP()
    {
        $viewName = basename($this->viewFile, '.php');
        include_once $this->viewFile;
        $control = new $viewName();
        return $control;
    }

    protected function processXML()
    {
        $content = file_get_contents($this->viewFile);
        return $content;
    }

    protected function processTemplate()
    {
        $baseName = basename($this->viewFile);
        $template = new MTemplate(dirname($this->viewFile));
        $template->context('manager', Manager::getInstance());
        $template->context('view', $this);
        $template->context('data', $this->data);
        $template->context('template', $template);
        return $template->fetch($baseName);
    }

    protected function processHTML()
    {
        return $this->processTemplate();
    }

    protected function processJS()
    {
        return $this->processTemplate();
    }

    protected function processWiki()
    {
        $wikiPage = file_get_contents($this->viewFile);
        $wiki = new MWiki();
        return $wiki->parse('', $wikiPage);
    }

    public function processPrompt(MPromptData $prompt)
    {
        $content = json_encode($prompt);
        $prompt->setContent($content);
    }

    public function processWindow()
    {
        $url = '';
        return $url;
    }

    public function processRedirect($url)
    {
        return $url;
    }


}
