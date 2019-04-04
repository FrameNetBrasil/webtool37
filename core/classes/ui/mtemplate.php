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
 * Brief Class Description.
 * Complete Class Description.
 */
class MTemplate //extends MComponent
{

    public $engine;
    public $context;
    public $path;
    public $template;
    private $templateEngine;

    public function __construct($path = '')
    {
        //parent::__construct();

        $this->path = $path;
        //mdump('*template path = ' . $path);
        if (function_exists('mb_internal_charset')) {
            mb_internal_charset('UTF-8');
        }

        $this->templateEngine = Manager::getOptions('templateEngine') ?: 'smarty';
        if ($this->templateEngine == 'smarty') {
            define('SMARTY_RESOURCE_CHAR_SET', 'UTF-8');
            $this->engine = new Smarty();
            $this->engine->setTemplateDir($path ? $path : Manager::getPublicPath() . '/templates');
            $this->engine->setCompileDir(Manager::getFrameworkPath() . '/var/templates');
            $this->engine->setCacheDir(Manager::getFrameworkPath() . '/var/cache');
            $this->engine->setConfigDir(Manager::getClassPath() . '/ui/smarty/configs');
            $this->engine->left_delimiter = '{{';
            $this->engine->right_delimiter = '}}';
        } elseif ($this->templateEngine == 'latte') {
            $this->engine = new \Latte\Engine;
            $this->engine->setTempDirectory(Manager::getConf("options.varPath") . '/maestro3/templates');
            $this->engine->getParser()->defaultSyntax = 'double';
            $this->engine->addFilter('translate', function ($s) {
                return _M($s);
            });
        }

        $this->context = array();
        $this->context('manager', Manager::getInstance());
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function context($key, $value)
    {
        $this->context[$key] = $value;
    }

    public function multicontext($context = [])
    {
        foreach ($context as $key => $value) {
            $this->context[$key] = $value;
        }
    }

    public function load($fileName)
    {
        $this->template = (($this->templateEngine == 'latte') ? $this->path . DIRECTORY_SEPARATOR : '') . $fileName;

    }

    public function render($args = array())
    {
        $params = array_merge($this->context, $args);
        if ($this->templateEngine == 'smarty') {
            foreach ($params as $name => $value) {
                $this->engine->assign($name, $value);
            }
            return $this->engine->fetch($this->template);
        }
        if ($this->templateEngine == 'latte') {
            return $this->engine->renderToString($this->template, $params);
        }

    }

    public function exists($fileName)
    {
        return file_exists($this->path . '/' . $fileName);
    }

    public function fetch($fileName, $args = array())
    {
        //mdump('=========fetch==='. $fileName);
        $this->load($fileName);
        return $this->render($args);
    }

    /*
     * Helper functions
     */

    private function parameters($control, $parameters = '')
    {
        $args = json_decode($parameters);
        foreach ($args as $k => $v) {
            if ($k{0} == '$') {
                $method = substr($k, 1);
                $control->$method($v);
            } else {
                $control->$k = $v;
            }
        }
    }

    public function link($text, $action, $parameters = '')
    {
        $a = new MLink('', $text, $action);
        $this->parameters($a, $parameters);
        return $a->generate();
    }

    public function control($class, $parameters = '')
    {
        $control = new $class;
        $this->parameters($control, $parameters);
        return $control->generate();
    }

    public function css($type, $value)
    {
        if ($type == 'file') {
            Manager::getPage()->addStyleSheet($value);
        } elseif ($type == 'code') {
            if (substr($value, -3) == 'css') {
                $value = file_get_contents($value);
            }
            Manager::getPage()->addStyleSheetCode($value);
        }
    }

    public function js($type, $value)
    {
        if ($type == 'file') {
            Manager::getPage()->addJsFile($value);
        } elseif ($type == 'script') {
            Manager::getPage()->addScriptURL($value);
        } elseif ($type == 'code') {
            if (substr($value, -2) == 'js') {
                $value = file_get_contents($value);
            }
            Manager::getPage()->addJsCode($value);
        }
    }

    public function file($type, $fileName)
    {
        if ($type == 'file') {
            $file = $this->path . '/' . $fileName;
        } elseif ($type == 'component') {
            $file = Manager::getAppPath('components/' . $fileName);
        }
        return $file;
    }
}
