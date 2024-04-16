<?php

use Jenssegers\Blade\Blade;

class MBlade
{

    private Blade $engine;
    private array $context;
    private array $paths;
    private string $template;

    public function __construct($paths)
    {
        $this->paths = $paths;
        //mdump('== template path: '.$path);
        $cachePath = Manager::getConf("options.varPath") . '/templates';
        $this->engine = new Blade($this->paths, $cachePath);
        $this->engine->addExtension('xml','blade');
        $this->engine->addExtension('vue','blade');
        if (function_exists('mb_internal_charset')) {
            mb_internal_charset('UTF-8');
        }
        $this->context = [];
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

    public function load(string $fileName)
    {
        //$this->template = $this->path . DIRECTORY_SEPARATOR . $fileName;
        $this->template = basename($fileName, '.blade.php');
    }

    public function render(array $args = []): string
    {
        $params = array_merge($this->context, $args);
        //mdump($this->path);
        //mdump($this->template);
        return $this->engine->render($this->template, $params);
    }

    public function exists($fileName)
    {
        return file_exists($this->path . '/' . $fileName);
    }

    public function fetch(string $templateName, array $args = []): string
    {
        //mdump('=========fetch==='. $fileName);
        //$this->load($fileName);
        $args['manager'] = Manager::getInstance();
        $args['data'] = Manager::getData();
        $args['page'] = Manager::getPage();
        $this->template = $templateName;
        return $this->render($args);
    }

}
