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

class MView extends MBaseView
{

    /**
     * Processa o arquivo da view e inclui o conteudo no objeto Page.
     * @param type $controller
     * @param type $parameters
     * @return type
     */
    public function process($controller, $parameters)
    {
        mtrace('view file = ' . $this->viewFile);
        $path = $this->getPath();
        Manager::addAutoloadPath($path);
        $extension = pathinfo($this->viewFile, PATHINFO_EXTENSION);
        $this->controller = $controller;
        $this->data = $parameters;
        $process = 'process' . $extension;
        $content = $this->$process();
        $page = Manager::getPage();
        $page->setContent($content);
        return (Manager::isAjaxCall() ? $page->generate() : $page->render());
    }

    protected function processPHP()
    {
        $viewName = basename($this->viewFile, '.php');
        include_once $this->viewFile;
        $control = new $viewName();
        $control->setView($this);
        //$control->load();
        return $control;
    }

    protected function processXML()
    {
        $container = new MBaseControl();
        $container->setView($this);
        $container->getControlsFromXML($this->viewFile);
        return $container;
    }

    protected function processTemplate()
    {
        $baseName = basename($this->viewFile);
        $template = new MTemplate(dirname($this->viewFile));
        $template->context('manager', Manager::getInstance());
        $template->context('page', Manager::getPage());
        $template->context('view', $this);
        $template->context('data', $this->data);
        $template->context('components', Manager::getAppPath('components'));
        $template->context('appURL', Manager::getAppURL());
        $template->context('template', $template);
        $template->context('isMaster', Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false');
        $template->context('isSenior', Manager::checkAccess('SENIOR', A_EXECUTE) ? 'true' : 'false');
        $template->context('isAnno', Manager::checkAccess('ANNO', A_EXECUTE) ? 'true' : 'false');

        //$template->context('painter', Manager::getPainter());
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
        $oPrompt = new \MPrompt(["type" => $prompt->type, "msg" => $prompt->message, "action1" => $prompt->action1, "action2" => $prompt->action2, "event1" => '', "event2" => '']);
        $page = Manager::getPage();
        $page->setName($oPrompt->getId());
        $page->setContent($oPrompt);
        //mdump($page->generate());
        if (Manager::isAjaxCall()) {
            $prompt->setContent($page->generate());
        } else {
            $prompt->setContent($page->render());
        }
        $prompt->setId($oPrompt->getId());
    }

    public function processWindow()
    {
        $page = Manager::getPage();
        return $page->window();
    }

}
