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

/**
 * MScripts.
 * Classe auxiliar para trabalhar com scripts associados a MPage.
 */
class MScripts extends MBase
{

    /**
     * Id da página à qual os scripts estão vinculados.
     * @var string
     */
    public $idPage;

    /**
     * URL dos scripts que devem ser carregados na página.
     * @var Nette\Utils\ArrayList
     */
    public $scripts;

    /**
     * Código a ser inserido no evento onLoad da página.
     * @var Nette\Utils\ArrayList
     */
    public $onload;

    /**
     * Código a ser inserido na função 'submit' (por formulário).
     * @var Nette\Utils\ArrayList
     */
    public $submit;

    /**
     * Código a ser inserido no evento onSubmit (por formulário).
     * @var Nette\Utils\ArrayList
     */
    public $onsubmit;

    /**
     * Código a ser inserido no evento onUnload da página.
     * @var Nette\Utils\ArrayList
     */
    public $onunload;

    /**
     * Código a ser inserido no evento onFocus da página.
     * @var Nette\Utils\ArrayList
     */
    public $onfocus;

    /**
     * Código a ser inserido no evento onError da página.
     * @var Nette\Utils\ArrayList
     */
    public $onerror;

    /**
     * Código a ser inserido diretamente no corpo da página.
     * @var Nette\Utils\ArrayList
     */
    public $jsCode;

    /**
     * Eventos que dem ser registrados via manager.registerEvents.
     * @var Nette\Utils\ArrayList
     */
    public $events;

    public function __construct($idPage = '')
    {
        parent::__construct();
        $this->idPage = $idPage;
        $this->onsubmit = [];
        $this->onload = [];
        $this->onerror = [];
        $this->onunload = [];
        $this->onfocus = [];
        $this->jsCode = [];
        $this->scripts = [];
        $this->events = [];
    }

    public function addScript($url, $module = null)
    {
        $url = Manager::getAbsoluteURL("public/scripts/{$url}", $module);
        $this->scripts[] = $url;
    }

    public function addScriptURL($url)
    {
        $this->scripts[] = $url;
    }

    public function addSubmit($jsCode, $idForm)
    {
        if (!$this->submit[$idForm]) {
            $this->submit[$idForm] = [];
        }
        $this->submit[$idForm][] = $jsCode;
    }

    public function addOnSubmit($jsCode, $idForm)
    {
        if (!$this->onsubmit[$idForm]) {
            $this->onsubmit[$idForm] = [];
        }
        $this->onsubmit[$idForm][] = $jsCode;
    }

    public function addEvent($event)
    {
        $this->events[$event->id][] = $event;
    }

    public function addOnLoad($jsCode)
    {
        $this->onload[] = $jsCode;
    }

    public function addOnUnLoad($jsCode)
    {
        $this->onunload[] = $jsCode;
    }

    public function addJsCode($jsCode)
    {
        $this->jsCode[] = $jsCode;
    }

    private function getScripts()
    {
        if (count($this->events) > 0) {
            $events = MJSON::encode($this->events);
            $this->addOnload("manager.registerEvents(" . $events . ");");
        }

        $scripts = new \StdClass;

        foreach ($this->scripts as $key => $url) {
            $scripts->scripts .= "\n manager.loader.load('{$url}');";
        }

        foreach ($this->jsCode as $key => $code) {
            $scripts->code .= "\n {$code}";
        }

        foreach ($this->onload as $key => $code) {
            $scripts->onload .= "\n {$code}";
        }

        $onsubmit = '';
        foreach ($this->onsubmit as $idForm => $list) {
            $onsubmit .= "manager.onSubmit[\"{$idForm}\"] = function() { \n";
            $onsubmit .= "    var result = ";
            $onsubmit .= implode(" && ", $list) . ";\n";
            $onsubmit .= "    return result;\n};\n";
        }
        $scripts->onsubmit = $onsubmit;
/*
        $submit = '';
        foreach ($this->submit as $idForm => $list) {
            $submit .= "manager.submit[\"{$idForm}\"] = function(element, url, idForm) { \n";
            $submit .= implode(" && ", $list) . ";\n";
            $submit .= "\n};\n";
        }
        $scripts->submit = $submit;
*/
        return $scripts;
    }
    
    public function generate()
    {
        $idPage = $this->idPage ?: Manager::getPage()->getName();
        $isAjax = Manager::isAjaxCall();
        $scripts = $this->getScripts();
        $hasCode = $scripts->scripts . $scripts->code . $scripts->onload . $scripts->onsubmit;
        if ($hasCode != '') {
            if ($isAjax) {
                $code = <<< HERE
<script type="text/javascript">
$scripts->scripts
$scripts->code

HERE;
                if ($scripts->onload != '') {
                    $code .= <<< HERE
manager.onLoad["{$idPage}"] = function() {
    console.log("inside onload {$idPage}");
    $scripts->onload;
};
HERE;
                }
                $code .= <<< HERE
$scripts->onsubmit
//-->
</script>
                
HERE;
            } else {
                $code = <<< HERE
<script type="text/javascript">
$scripts->scripts
$scripts->code

HERE;
           if ($scripts->onload != '') {
               $code .= <<< HERE
    manager.ready = function() {
        jQuery(function($) {
            console.log("inside onload {$idPage}");
            $scripts->onload;
        });
    };

HERE;
           }
           $code .= <<< HERE
$scripts->onsubmit
//-->
</script>
                
HERE;
            }
            return "<div id=\"{$idPage}\" class=\"mScripts\">{$code}</div>";
        } else {
            return '';
        }
    }

}

?>