<?php
class MSession
{
    private $session;
    private $app;
    private $container;
    private $timeout;

    /**
     * Cada app deve ter seu proprio container para a sessão.
     * MSession constructor.
     * @param string $app
     */
    public function __construct($app = '')
    {
        $session_factory = new \Aura\Session\SessionFactory;
        $this->session = $session_factory->newInstance($_COOKIE);
        $this->app = $app ?: Manager::getInstance()->app;
        $this->container = $this->session->getSegment($this->app . '-session');
    }

    public function __get($var)
    {
        return $this->container->get($var);
    }

    public function __set($var, $value)
    {
        $this->container->set($var, $value);
    }

    public function init($sid = '')
    {
        try {
            //if ($sid != '') {
            //    parent::setId($sid);
            //}
            //parent::start();
            //$this->container = $this->container($this->app);
            $timestamp = $this->timestamp;
            if (!$timestamp) {
                $this->timestamp = time();
            }
        } catch (EMException $e) {
            throw $e;
        }
    }

    public function checkTimeout($exception = false)
    {
        $timeout = Manager::getConf('session.timeout');
        // If 0, we are not controlling session duration
        if ($timeout != 0) {
            $timestamp = time();
            $difftime = $timestamp - $this->timestamp;
            $this->timeout = ($difftime > ($timeout * 60));
            $this->timestamp = $timestamp;
            if ($this->timeout) {
                $this->session->destroy();
                if ($exception) {
                    throw new ETimeOutException();
                } else {
                    return true;
                }
            }
        }
        return false;
    }

    public function container($namespace)
    {
        return $this->session->getSegment($namespace);
    }

    public function get($var)
    {
        return $this->container->get($var);
    }

    public function set($var, $value)
    {
        $this->container->set($var, $value);
    }

    public function freeze()
    {
        $this->session->commit();
    }

    public function destroy() {
        $this->session->destroy();
    }

    public function getValue($var)
    {
        return $this->get($var);
    }

    public function setValue($var, $value)
    {
        $this->set($var, $value);
    }

}
