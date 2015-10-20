<?php namespace PortOneFive\Essentials\Eloquent\Exceptions;

use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Contracts\Validation\ValidationException;

class InvalidMetaException extends ValidationException
{
    /**
     * Create a new validation exception instance.
     *
     * @param  \Illuminate\Contracts\Support\MessageProvider $provider
     */
    public function __construct(MessageProvider $provider)
    {
        parent::__construct($provider);

        foreach ($this->errors()->getMessageBag()->all() as $error)
        {
            $this->message .= $error . "\n";
        }
    }
}
