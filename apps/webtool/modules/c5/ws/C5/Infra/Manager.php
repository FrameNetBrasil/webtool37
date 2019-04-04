<?php

namespace C5\Infra;

class Manager
{
    public $debug;
    public $logger;
    public $grammarPath;
    public $importPath;
    public $fullNetwork;
    public $typeNetwork;
    public $tokenNetwork;
    public $regionNetwork;
    public $conceptNetwork;
    public $rhNetwork;
    public $udNetwork;
    public $words;
    static private $instance = null;
    private $container = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::createInstance();
        }
        return self::$instance;
    }

    private static function createInstance()
    {
        self::$instance = new Manager();
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function logMessage($msg)
    {
        $this->dump($msg);
    }

    public function setLogLevel($level)
    {
        $this->debug = $level;
    }

    public function dump($msg)
    {
        if ($this->debug) {
            //print_r($msg);
            //print_r("\n");
            $this->logger->info(print_r($msg, TRUE));
        }
    }

    public function createFullNetwork()
    {
        if (isset($this->fullNetwork)) {
            $this->fullNetwork->clearAll();
        }
        unset($this->fullNetwork);
        if (isset($this->tokenNetwork)) {
            $this->tokenNetwork->clearAll();
        }
        unset($this->tokenNetwork);
        if (isset($this->conceptNetwork)) {
            $this->conceptNetwork->clearAll();
        }
        unset($this->conceptNetwork);
        $this->fullNetwork = $this->container->get('FullNetwork');
        return $this->fullNetwork;
    }

    public function createTypeNetwork()
    {
        if (isset($this->typeNetwork)) {
            $this->typeNetwork->clearAll();
        }
        unset($this->typeNetwork);
        if (isset($this->tokenNetwork)) {
            $this->tokenNetwork->clearAll();
        }
        unset($this->tokenNetwork);
        $this->typeNetwork = $this->container->get('TypeNetwork');
        return $this->typeNetwork;
    }

    public function getTypeNetwork()
    {
        return $this->typeNetwork;
    }

    public function createConceptNetwork()
    {
        if (isset($this->conceptNetwork)) {
            $this->conceptNetwork->clearAll();
        }
        unset($this->conceptNetwork->typeNetwork);
        unset($this->conceptNetwork);
        $this->conceptNetwork = $this->container->make('ConceptNetwork');
        return $this->conceptNetwork;
    }

    public function createTokenNetwork()
    {
        if (isset($this->tokenNetwork)) {
            $this->tokenNetwork->clearAll();
        }
        unset($this->tokenNetwork);
        $this->tokenNetwork = $this->container->get('TokenNetwork');
        $this->tokenNetwork->setTypeNetwork($this->typeNetwork);
        return $this->tokenNetwork;
    }

    public function createRegionNetwork()
    {
        if (isset($this->regionNetwork)) {
            $this->regionNetwork->clearAll();
        }
        unset($this->regionNetwork);
        $this->regionNetwork = $this->container->get('RegionNetwork');
        $this->regionNetwork->setTokenNetwork($this->tokenNetwork);
        return $this->regionNetwork;
    }

    public function deleteFiles(string $path, $prefix = '')
    {
        $scandir = scandir($path) ?: [];
        $scandir = array_diff($scandir, ['..', '.']);
        foreach ($scandir as $path) {
            if (is_file($path)) {
                if (($prefix != '') && ($path{0} != $prefix)) {
                    continue;
                }
                $fullpath = $path . '/' . $path;
                $this->dump('deleting: ' . $fullpath);
                unlink($fullpath);
            }
        }
    }

}

