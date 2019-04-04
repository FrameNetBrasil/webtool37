<?php
interface ISubscriber
{
    public function notify($event, $publisher, $data);
}