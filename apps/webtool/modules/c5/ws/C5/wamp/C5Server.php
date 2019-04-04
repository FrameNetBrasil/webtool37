<?php
use C5\Service\C5Service;

class C5Server extends \Thruway\Peer\Client {

    protected $clients;
    protected $service;
    protected $typeNetwork;

    public function __construct($realm, LoopInterface $loop = null)
    {
        parent::__construct($realm, $loop);
        $this->clients = [];
    }

    private function instantiateService($caller) {
        print_r("========================== Instantiating Server ========================\n");
        $this->clients[$caller] = $this->service = new C5Service();
        $this->typeNetwork = false;
    }

    private function initService($caller) {
        $this->service = isset($this->clients[$caller]) ? $this->clients[$caller] : '';
        if ($this->service == '') {
            $this->instantiateService($caller);
        }
    }

    public function resetNetwork($args, $kwargs, $details)
    {
        $this->instantiateService($details->caller);
        $json = $this->service->resetNetwork($args[0]);
        return $json;
    }

    public function generateFullNetwork($args, $kwargs, $details)
    {
        $this->initService($details->caller);
        $json = $this->service->generateFullNetwork($args[0], $args[1]);
        return $json;
    }

    public function loadFullNetwork($args, $kwargs, $details)
    {
        $this->initService($details->caller);
        $json = $this->service->loadFullNetwork($args[0], $args[1]);
        return $json;
    }

    public function showTypeNetwork($args, $kwargs, $details)
    {
        $this->initService($details->caller);
        $json = $this->service->showTypeNetwork($args[0]);
        return $json;
    }

    public function buildTokenNetwork($args, $kwargs, $details)
    {
        $this->initService($details->caller);
        $json = $this->service->buildTokenNetwork($args[0]);
        return $json;
    }

    public function initIteration($args, $kwargs, $details)
    {
        $this->initService($details->caller);
        $json = $this->service->initInteration($args[0]);
        return $json;
    }

    public function nextIteration($args, $kwargs, $details)
    {
        $json = $this->service->nextInteration($args[0]);
        return $json;
    }

    public function fullActivation($args, $kwargs, $details)
    {
        $this->initService($details->caller);
        $json = $this->service->fullActivation($args[0]);
        return $json;
    }

    public function onSessionStart($session, $transport) {
        $procedures = [
            'resetNetwork',
            'generateFullNetwork',
            'loadFullNetwork',
            'showTypeNetwork',
            'buildTokenNetwork',
            'initIteration',
            'nextIteration',
            'fullActivation',
        ];
        foreach($procedures as $procedure) {
            $fqn = "br.ufjf.framenetbr.c5.{$procedure}";
            $this->getCallee()->register($session, $fqn, array($this, $procedure),['disclose_caller' => true]);
        }
    }

}