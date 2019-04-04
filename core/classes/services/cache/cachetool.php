<?php

class CacheTool
{
    /** @var MCacheService */
    private $cacheDevice;
    private static $instance;

    private function __construct()
    {
        $this->cacheDevice = MCacheService::getCacheServiceByName('MMemoryCache');
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function getAndStore($param, \Closure $factory, $context = null)
    {
        $key = is_scalar($param) ? $param : md5(serialize($param));
        if (!$this->keyExists($key, $context)) {
            $value = call_user_func($factory, $param);
            $this->set($key, $value, $context);
        }

        return $this->get($key, $context);
    }

    public function getModel($class, $id)
    {
        return $this->get($id, $class);
    }

    public function setModel(MBusinessModel $model)
    {
        if ($model->isPersistent()) {
            $this->set($model->getId(), $model, $model);
        }

        return $this;
    }

    public function keyExists($key, $context = null)
    {
        return $this->get($key, $context) !== null;
    }

    public function get($key, $context = null)
    {
        $key = $this->buildKey($key, $context);

        return $this->cacheDevice->get($key);
    }

    public function set($key, $value, $context = null)
    {
        $key = $this->buildKey($key, $context);
        $this->cacheDevice->set($key, $value);

        return $this;
    }

    private function buildKey($id, $context)
    {
        if ($context == null) {
            return $id;
        }

        if (is_object($context)) {
            $context = get_class($context);
        }

        return md5($context) . "_$id";
    }
}