<?php namespace PortOneFive\Essentials\Eloquent\Relations;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\MessageBag;
use PortOneFive\Essentials\Dictionary;
use PortOneFive\Essentials\Eloquent\Exceptions\InvalidMetaException;

class HasMeta extends HasMany {

    /**
     * @var Factory
     */
    protected static $validator;

    /**
     * @var array
     */
    protected $rules         = [];
    protected $accepts       = [];
    protected $saveListeners = [];

    /**
     * @var
     */
    private $keyColumn;

    /**
     * @var
     */
    private $valueColumn;

    /**
     * Create a new has one or many relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  \Illuminate\Database\Eloquent\Model   $parent
     * @param  string                                $foreignKey
     * @param  string                                $localKey
     * @param                                        $keyColumn
     * @param                                        $valueColumn
     */
    public function __construct(Builder $query, Model $parent, $foreignKey, $localKey, $keyColumn, $valueColumn)
    {
        parent::__construct($query, $parent, $foreignKey, $localKey);

        $this->keyColumn   = $keyColumn;
        $this->valueColumn = $valueColumn;

        self::$validator = app()->make(Factory::class);
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        $metaBag = new Dictionary($this->query->lists($this->valueColumn, $this->keyColumn)->all());

        $this->registerMetaBagListeners($metaBag, $this->getParent());

        return $metaBag;
    }

    /**
     * @param array $rules
     *
     * @return $this
     */
    public function withRules(array $rules)
    {
        $this->rules = $rules;

        return $this;
    }

    public function acceptsOnly(array $keys)
    {
        $this->accepts = $keys;

        return $this;
    }

    public function acceptsAny()
    {
        $this->accepts = [];

        return $this;
    }

    /**
     * @param $key
     * @param $value
     */
    protected function createNewEntry($key, $value)
    {
        $this->create([$this->keyColumn => $key, $this->valueColumn => $value]);
    }

    /**
     * @param $key
     * @param $value
     */
    protected function updateEntry($key, $value)
    {
        $this->where($this->keyColumn, $key)->update([$this->valueColumn => $value]);
    }

    /**
     * @param $key
     */
    protected function deleteEntry($key)
    {
        $this->where($this->keyColumn, $key)->delete();
    }

    /**
     * @param $key
     * @param $value
     */
    protected function validateEntry($key, $value)
    {
        if ( ! $this->isAccepted($key))
        {
            throw new InvalidMetaException(new MessageBag([$key => "The key {$key} is not a valid key."]));
        }

        if (isset($this->rules[$key]))
        {
            $validator = self::$validator->make([$key => $value], array_only($this->rules, $key));

            if ($validator->fails())
            {
                throw new InvalidMetaException($validator->getMessageBag());
            }
        }
    }

    /**
     * @param $key
     *
     * @return bool
     */
    protected function isAccepted($key)
    {
        return empty($this->accepts) || in_array($key, $this->accepts);
    }

    /**
     * @param Dictionary $metaBag
     * @param Model      $parent
     */
    protected function registerMetaBagListeners(Dictionary $metaBag, Model $parent)
    {
        $parent->saved(
            function () use ($metaBag)
            {

                if (empty($this->saveListeners))
                {
                    return;
                }

                $this->validateBag($metaBag);

                foreach ($this->saveListeners as $listener)
                {
                    list($callback, $payload) = $listener;

                    call_user_func_array($callback, $payload);
                }
            }
        );

        $metaBag->updating(
            function ($key, $value)
            {
                $this->validateEntry($key, $value);
            }
        );

        $metaBag->updated(
            $this->enqueueSaveListener(
                function ($key, $value, $oldValue)
                {
                    $this->updateEntry($key, $value);
                }
            )
        );

        $metaBag->creating(
            function ($key, $value)
            {
                $this->validateEntry($key, $value);
            }
        );

        $metaBag->created(
            $this->enqueueSaveListener(
                function ($key, $value)
                {
                    $this->createNewEntry($key, $value);
                }
            )
        );

        $metaBag->deleting(
            function ($key)
            {
                $this->validateEntry($key, null);
            }
        );

        $metaBag->deleted(
            function ($key)
            {
                $this->deleteEntry($key);
            }
        );
    }

    /**
     * @param Dictionary $metaBag
     */
    protected function validateBag(Dictionary $metaBag)
    {
        if (empty($this->rules))
        {
            return;
        }

        $validator = self::$validator->make($metaBag->toArray(), $this->rules);

        if ($validator->fails())
        {
            throw new InvalidMetaException($validator->getMessageBag());
        }
    }

    protected function enqueueSaveListener($callback)
    {
        return function () use ($callback)
        {
            $this->saveListeners[] = [$callback, func_get_args()];
        };
    }
}
