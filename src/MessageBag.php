<?php namespace PortOneFive\Essentials;

use Illuminate\Session\Store;
use Illuminate\Support\MessageBag as BaseMessageBag;

class MessageBag extends BaseMessageBag
{
    /**
     * @var Store
     */
    protected $session;

    public function __construct(array $messages = [])
    {
        $this->session = app()->make(Store::class);
        BaseMessageBag::__construct($messages);
    }

    /**
     * @param $key
     * @param $message

     *
*@return BaseMessageBag
     */
    public function flash($key, $message)
    {
        $this->session->flash(
            'flashMessages',
            $this->session->get('flashMessages', new BaseMessageBag)->add($key, $message)
        );

        return $this;
    }

    /**
     * @return BaseMessageBag
     */
    public function getFlashedMessages()
    {
        return $this->session->get('flashMessages', new BaseMessageBag);
    }
}