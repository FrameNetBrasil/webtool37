<?php

/**
 * Class EventManager
 *
 * Classe com a estrutura básica para permitir o padrão observer ou publisher/subscriber.
 *
 * Os subscribers devem implementar a interface ISubscriber e são definidos na configuração 'eventSubscribers' com o
 * formato:
 *
 *  'classe\do\subscriber' => 'nomeDoEvento'
 *
 * Para evitar conflitos convencionou-se o nome de evento como => modulo:model:evento
 */
class MEventManager
{
    private $subscribers = [];

    private function __construct()
    {
        $this->loadSubscribers();
    }

    public static function publish($event, $publisher = null, $data = [], $ignoreErrors = true)
    {
        $instance = new self;
        $instance->doPublish($event, $publisher, $data, $ignoreErrors);
    }

    private function doPublish($event, MBusinessModel $publisher, $data, $ignoreErrors)
    {
        try {
            foreach ($this->subscribers[$event] as $subscriber) {
                $subscriber->notify($event, $publisher, $data);
            }
        } catch (\Exception $ex) {
            if ($ignoreErrors) {
                \Manager::logError($ex->getTraceAsString());
            } else {
                throw $ex;
            }
        }

    }

    /**
     * Carregamento de todos os subscribers que estão no conf. Se o número de subscribers crescer ao ponto de tornar o
     * carregamento oneroso existe a possibilidade de carregar somente os subscribers de um determinado evento no método
     * doPublish.
     */
    private function loadSubscribers()
    {
        foreach ($this->getSubscriberList() as $class => $event) {
            if (class_exists($class)) {
                $this->subscribe(new $class, $event);
            } else {
                mtrace("A classe do subscriber $class não foi encontrada!");
            }
        }

    }

    private function getSubscriberList()
    {
        $app = Manager::getApp();
        $file = Manager::getAbsolutePath("apps/$app/conf/subscribers.php");

        return file_exists($file) ? include($file) : [];
    }


    private function subscribe(ISubscriber $subscriber, $event)
    {
        $this->subscribers[$event][] = $subscriber;
    }
}