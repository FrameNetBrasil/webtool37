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
 * MPage
 * Representa a pagina a ser renderizada, incluindo o código HTML, scripts e folhas de estilos.
 * A renderização pode seguir um template ou não. O template default é indicado na configuração conf.php. 
 */
class MPage extends MBase
{

    /**
     * Lista de scripts a serem renderizados.
     * @var MScripts
     */
    public $scripts;

    /**
     *
     * @var <type>
     */
    public $redirectTo;

    /**
     *
     * @var <type>
     */
    public $fileUpload;

    /**
     *
     * @var <type>
     */
    public $window;

    /**
     *
     * @var <type>
     */
    public $prompt;

    /**
     *
     * @var <type>
     */
    public $binary;

    /**
     *
     * @var <type>
     */
    public $download;

    /**
     * Objeto Template usado na renderização.
     * @var MTemplate
     */
    public $template;

    /**
     * Nome do Template usado na renderização. Default: 'index'.
     * @var string
     */
    public $templateName = 'index';

    /**
     * Conteúdo da página. Pode ser um controle, um array de controles ou código HTML.
     * @var array
     */
    public $content;

    /**
     * Código CSS a ser incluído na página.
     * @var string 
     */
    public $styleSheetCode;

    /**
     * Tipo informado à classe de renderização (em mvc\results).
     * @var string 
     */
    public $renderType;

    /**
     * Id informado à classe de renderização (em mvc\results).
     * @var string 
     */
    public $renderId;

    /**
     * Código HTML gerado por tdump() a ser incluido na página.
     * @var string
     */
    public $dump;

    public function __construct()
    {
        parent::__construct('page' . uniqid());
        $this->scripts = new MScripts();
        $this->fileUpload = mrequest('__ISFILEUPLOAD') == 'yes';
        $this->content = new MBaseControl();
        $template = mrequest('__TEMPLATE') ? : (Manager::getConf('theme.template')? : 'index');
        $this->setTemplateName($template);
        $this->setTemplate();
        $this->styleSheetCode = '';
        $this->renderType = "page";
        $this->renderId = $this->name;
        ob_start();
    }

    /**
     * Template methods
     */

    /**
     * Define template and template variables
     */
    public function setTemplate()
    {
        $path = Manager::getThemePath();
        $this->template = new MTemplate($path);
        $this->template->context('manager', Manager::getInstance());
        $this->template->context('page', $this);
        $this->template->context('charset', Manager::getOptions('charset'));
        $this->template->context('template', $this->template);
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getTemplateName()
    {
        return $this->templateName;
    }

    public function setTemplateName($name)
    {
        $this->templateName = $name;
    }

    /**
     * is* methods
     */
    public function isPostBack()
    {
        return Manager::getRequest()->isPostBack();
    }

    /*
      CSS Styles
     */

    public function addStyleSheet($fileName)
    {
        $file = Manager::getFrameworkPath('var/files/' . basename($fileName));
        copy($fileName, $file);
        $url = Manager::getDownloadURL('cache', basename($fileName), true);
        $this->onLoad("$('head').append('<link href=\"{$url}\" type=\"text/css\" rel=\"stylesheet\" />');");
    }

    public function addStyleSheetCode($code)
    {
        if (Manager::isAjaxCall()) {
            $fileName = md5($code) . '.css';
            $file = Manager::getFrameworkPath('var/files/' . $fileName);
            file_put_contents($file, $code);
            $url = Manager::getDownloadURL('cache', $fileName, true);
            $this->onLoad("$('head').append('<link href=\"{$url}\" type=\"text/css\" rel=\"stylesheet\" />');");
        } else {
            $this->styleSheetCode .= "\n" . $code;
        }
    }

    /*
      Scripts - Métodos de fachada para MScripts.
     */

    public function addScript($url, $module = null)
    {
        $this->scripts->addScript($url, $module);
    }

    public function addScriptURL($url)
    {
        $this->scripts->addScriptURL($url);
    }

    public function getScripts()
    {
        return $this->scripts->scripts;
    }

    public function getOnLoad()
    {
        return $this->scripts->onload;
    }

    public function getOnError()
    {
        return $this->scripts->onerror;
    }

    public function getOnSubmit()
    {
        return $this->scripts->onsubmit;
    }
    
    public function getOnUnLoad()
    {
        return $this->scripts->onunload;
    }

    public function getOnFocus()
    {
        return $this->scripts->onfocus;
    }

    public function getJsCode()
    {
        return $this->scripts->jsCode;
    }

    public function submit($jsCode, $formId)
    {
        $this->scripts->addSubmit($jsCode, $formId);
    }

    public function onSubmit($jsCode, $formId)
    {
        $this->scripts->addOnSubmit($jsCode, $formId);
    }

    public function onLoad($jsCode)
    {
        $this->scripts->addOnLoad($jsCode);
    }

    public function onUnLoad($jsCode)
    {
        $this->scripts->addOnUnload($jsCode);
    }

    public function onError($jsCode)
    {
        $this->scripts->addOnError($jsCode);
    }

    public function onFocus($jsCode)
    {
        $this->scripts->addOnLoad($jsCode);
    }

    public function addJsCode($jsCode)
    {
        $this->scripts->addJsCode($jsCode);
    }

    public function addJsFile($fileName)
    {
        $jsCode = file_get_contents($fileName);
        $this->scripts->addJsCode($jsCode);
    }

    /*
      Events
     */

    public function registerEvent($id, $event, $function, $preventDefault = "true")
    {
        $register = (object) [
                    "id" => $id,
                    "event" => $event,
                    "preventDefault" => $preventDefault
        ];
        if ($function{0} == '!') { // handler já é um eventHandler javascript
            $register->handler = substr($function, 1);
        } else {
            $register->handler = $function;
        }
        $this->scripts->addEvent($register);
    }

    /*
     * Properties
     */

    public function setTitle($value)
    {
        $this->property->title = $value;
    }

    public function getTitle()
    {
        return $this->property->title;
    }

    /*
     * Dump
     */

    public function addDump($dump)
    {
        $this->dump.= $dump;
    }

    /*
      Response related methods
     */

    public function redirect($url)
    {
        $this->redirectTo = $url;
    }

    public function window($url)
    {
        $this->window = $url;
    }

    public function binary($stream)
    {
        $this->binary = $stream;
    }

    public function download($fileName)
    {
        $this->download = $fileName;
    }

    public function prompt($prompt)
    {
        $this->prompt = $prompt;
    }

    /**
     * Generate methods
     */

    /**
     * Gera o código HTML do conteúdo criado pela execução da requisição.
     * Este método é usado para gerar o contéudo criado através da execução da requisição, ou para gerar o código relativo a um componente.
     * usado nas respostas às requisições via Ajax.
     * @param string $element Elemento a ser gerado ('content' ou nome do componente).
     * @return string Código HTML gerado.
     */
    public function generate($element = 'content')
    {
        $html = '';
        if ($element == 'content') {
            $html = $this->generateContent() . $this->generateStyleSheetCode() . $this->generateScripts();
        } else {
            $component = new $element;
            $html = $component->generate();
        }
        if ($this->dump) {
            $html = $this->dump . $html;
        }
        return $html;
    }

    public function generateStyleSheetCode()
    {
        $code = ($this->styleSheetCode != '') ? "<style type=\"text/css\">" . $this->styleSheetCode . "\n</style>\n" : '';
        return $code;
    }

    public function generateScripts()
    {
        return $this->scripts->generate($this->getName());
    }

    public function fetch($template = '')
    {
        $template = $template ? : $this->getTemplateName();
        $html = $template != '' ? $this->template->fetch($template . '.html') : $this->generate();
        return $html;
    }

    /**
     * Gera o código HTML da página-resposta à execução da requisição.
     * Este método usa um template para gerar a página HTML enviada como resposta a uma requisição não-Ajax.
     * @param string $template Template a ser renderizado.
     * @return string Código HTML gerado.
     */
    public function render($template = '')
    {
        $html = $this->fetch($template);
        if ($ob = ob_get_clean()) {
            $html = $ob . $html;
        }
        if ($this->dump) {
            $html = $this->dump . $html;
        }
        return $html;
    }

    /**
     * Content
     */
    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function clearContent()
    {
        $this->content = '';
    }

    public function generateContent()
    {
        return MBasePainter::generateToString($this->content);
    }

}
