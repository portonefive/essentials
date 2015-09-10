<?php namespace PortOneFive\Essentials\Messaging;

trait SendsMessages
{
    public function alert($message, $class = 'success')
    {
        return alert($message, $class);
    }

    public function notify($message, $class = 'info')
    {
        return notify($message, $class);
    }

    public function error($message, $class = 'error')
    {
        return error($message, $class);
    }
}
