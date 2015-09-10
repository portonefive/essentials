<?php namespace PortOneFive\Essentials\Events;

use Illuminate\Contracts\Events\Dispatcher;

trait DispatchesEvents
{
    /**
     * The Dispatcher instance.
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Dispatch all events for an entity.
     *
     * @param object $entity
     */
    public function dispatchEventsFor($entity)
    {
        if (method_exists($entity, 'releaseEvents')) {
            $this->dispatchEvents($entity->releaseEvents());
        }
    }

    protected function dispatchEvents(array $events)
    {
        foreach ($events as $event) {
            $this->dispatchEvent($event);
        }
    }

    protected function dispatchEvent($event)
    {
        $this->getDispatcher()->fire($event);
    }

    /**
     * Get the event dispatcher.
     *
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher ?: $this->dispatcher = app()->make(Dispatcher::class);
    }

    /**
     * Set the dispatcher instance.
     *
     * @param mixed $dispatcher
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}
