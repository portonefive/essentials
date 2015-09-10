<?php namespace PortOneFive\Essentials;

use ArrayAccess;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use JsonSerializable;

class Dictionary implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected static $dispatcher;

    /**
     * User exposed observable events.
     *
     * @var array
     */
    protected $observables = [];

    /**
     * The dictionary-formed data
     *
     * @var Collection
     */
    protected $data;

    protected $dirty = [];

    /**
     * @param array|Collection $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;

        if ( ! $this->data instanceof Collection) {
            $this->data = Collection::make($this->data);
        }
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->data->all();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data->toArray();
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function has($key)
    {
        return $this->data->has($key);
    }

    public function contains($key)
    {
        return $this->data->contains($key);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->data->get($key, $default);
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        if ($this->has($key)) {
            $this->update($key, $value);
        } else {
            $this->create($key, $value);
        }

        $this->dirty[] = $key;
    }

    public function merge(array $values)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function setRaw(array $values)
    {
        foreach ($this->toArray() as $key => $value) {
            $this->forget($key);
        }

        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function forget($key)
    {
        if ($this->fireEvent('deleting', [$key]) === false) {
            return false;
        }

        $this->data->forget($key);

        $this->fireEvent('deleted', [$key], false);

        $this->dirty[] = $key;

        return true;
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function __unset($key)
    {
        return $this->forget($key);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * @return int
     */
    public function isDirty()
    {
        return count($this->dirty);
    }

    /**
     * @return array
     */
    public function getDirty()
    {
        return $this->dirty;
    }

    /**
     * @param $keys
     *
     * @return array
     */
    public function only($keys)
    {
        $only = is_array($keys) ? $keys : func_get_args();

        return array_only($this->toArray(), $only);
    }

    /**
     * Cast an attribute to a native PHP type.
     *
     * @param  mixed $value
     *
     * @param        $castType
     *
     * @return mixed
     */
    protected function castValue($value, $castType)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($castType) {
            case 'int':
            case 'integer':
                return (int)$value;
            case 'real':
            case 'float':
            case 'double':
                return (float)$value;
            case 'string':
                return (string)$value;
            case 'bool':
            case 'boolean':
                return (bool)$value;
            case 'object':
                return json_decode($value);
            case 'array':
            case 'json':
                return json_decode($value, true);
            case 'collection':
                return new Collection(json_decode($value, true));
            default:
                return $value;
        }
    }


    /**
     * Register a creating event with the dispatcher.
     *
     * @param  \Closure|string $callback
     * @param  int             $priority
     *
     * @return void
     */
    public static function creating($callback, $priority = 0)
    {
        static::registerEvent('creating', $callback, $priority);
    }

    /**
     * Register a created event with the dispatcher.
     *
     * @param  \Closure|string $callback
     * @param  int             $priority
     *
     * @return void
     */
    public static function created($callback, $priority = 0)
    {
        static::registerEvent('created', $callback, $priority);
    }

    /**
     * Register a updating event with the dispatcher.
     *
     * @param  \Closure|string $callback
     * @param  int             $priority
     *
     * @return void
     */
    public static function updating($callback, $priority = 0)
    {
        static::registerEvent('updating', $callback, $priority);
    }

    /**
     * Register a updated event with the dispatcher.
     *
     * @param  \Closure|string $callback
     * @param  int             $priority
     *
     * @return void
     */
    public static function updated($callback, $priority = 0)
    {
        static::registerEvent('updated', $callback, $priority);
    }

    /**
     * Register a deleting event with the dispatcher.
     *
     * @param  \Closure|string $callback
     * @param  int             $priority
     *
     * @return void
     */
    public static function deleting($callback, $priority = 0)
    {
        static::registerEvent('deleting', $callback, $priority);
    }

    /**
     * Register a deleted event with the dispatcher.
     *
     * @param  \Closure|string $callback
     * @param  int             $priority
     *
     * @return void
     */
    public static function deleted($callback, $priority = 0)
    {
        static::registerEvent('deleted', $callback, $priority);
    }

    /**
     * Get the observable event names.
     *
     * @return array
     */
    public function getObservableEvents()
    {
        return array_merge(
            [
                'creating',
                'created',
                'updating',
                'updated',
                'deleting',
                'deleted',
            ],
            $this->observables
        );
    }

    /**
     * Fire the given event.
     *
     * @param  string $event
     * @param array   $data
     * @param  bool   $halt
     *
     * @return mixed
     */
    protected function fireEvent($event, array $data = [], $halt = true)
    {
        if ( ! isset(static::$dispatcher)) {
            return true;
        }

        // We will append the names of the class to the event to distinguish it from
        // other model events that are fired, allowing us to listen on each model
        // event set individually instead of catching event for all the models.
        $event = "portonefive.meta-bag.{$event}: " . get_class($this);

        $method = $halt ? 'until' : 'fire';

        return static::$dispatcher->$method($event, $data);
    }

    /**
     * Remove all of the event listeners.
     *
     * @return void
     */
    public static function flushEventListeners()
    {
        if ( ! isset(static::$dispatcher)) {
            return;
        }

        $instance = new static;

        foreach ($instance->getObservableEvents() as $event) {
            static::$dispatcher->forget("portonefive.meta-bag.{$event}: " . get_called_class());
        }
    }

    /**
     * Register a model event with the dispatcher.
     *
     * @param  string          $event
     * @param  \Closure|string $callback
     * @param  int             $priority
     *
     * @return void
     */
    protected static function registerEvent($event, $callback, $priority = 0)
    {
        if (isset(static::$dispatcher)) {
            $name = get_called_class();

            static::$dispatcher->listen("portonefive.meta-bag.{$event}: {$name}", $callback, $priority);
        }
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher
     */
    public static function getEventDispatcher()
    {
        return static::$dispatcher;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $dispatcher
     *
     * @return void
     */
    public static function setEventDispatcher(Dispatcher $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }

    /**
     * Unset the event dispatcher for models.
     *
     * @return void
     */
    public static function unsetEventDispatcher()
    {
        static::$dispatcher = null;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return bool
     */
    protected function update($key, $value)
    {
        $oldValue = $this->data->get($key);
        $payload  = [$key, $value, $oldValue];

        if ($this->fireEvent('updating', $payload) === false) {
            return false;
        }

        $this->data->put($key, $value);

        $this->fireEvent('updated', $payload, false);

        return true;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return bool
     * @throws \Exception
     */
    protected function create($key, $value)
    {
        if ($this->fireEvent('creating', [$key, $value]) === false) {
            return false;
        }

        $this->data->put($key, $value);

        $this->fireEvent('created', [$key, $value], false);

        return true;
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->forget($offset);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }

    public function push()
    {
    }
}
