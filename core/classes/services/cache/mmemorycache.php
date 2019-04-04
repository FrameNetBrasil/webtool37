<?php

/**Cache simples que armazena em memória e dura somente o tempo da requisição */
class MMemoryCache extends MCacheService
{
    private $cache = [];

    public function add($name, $value, $ttl = -1)
    {
        if (isset($this->cache[$name])) {
            throw new \Exception("A chave já existe!");
        }

        $this->set($name, $value, $ttl);
    }

    public function set($name, $value, $ttl = -1)
    {
        $this->cache[$name] = $value;
    }

    public function get($name)
    {
        return array_key_exists($name, $this->cache) ? $this->cache[$name] : null;
    }

    public function increment($name, $by = 1)
    {
        throw new \Exception('Not implemented');
    }

    public function decrement($name, $by = 1)
    {
        throw new \Exception('Not implemented');
    }

    public function deleteMultiple(array $keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }

    public function delete($name)
    {
        unset($this->cache[$name]);
    }

    public function clear()
    {
        $this->cache = [];
    }

    public function getKeys($pattern = '*')
    {
        throw new \Exception('Not implemented');
    }

    public function getAllKeys()
    {
        throw new \Exception('Not implemented');
    }

    public function serviceIsAvailable()
    {
        return true;
    }
}