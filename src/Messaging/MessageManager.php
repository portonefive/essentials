<?php namespace PortOneFive\Essentials\Messaging;

use Illuminate\Http\RedirectResponse;
use Illuminate\Session\Store;
use Illuminate\Support\Collection;

class MessageManager
{

    /**
     * @var array
     */
    protected static $bags = ['error', 'notice', 'alert', 'message'];

    /**
     * @var Collection
     */
    protected $messageBags = [];

    /**
     * @var
     */
    protected $session;

    /**
     * @param Store $session
     */
    public function __construct(Store $session)
    {
        $this->session = $session;

        $this->messageBags = new Collection($this->session->pull('__bags', []));

        app('router')->after(
            function ($request, $response) {
                if ($response instanceof RedirectResponse) {

                    if ($response->getSession()->has('errors')) {
                        $errors = $response->getSession()->get('errors')->getBag('default')->getMessages();

                        foreach ($errors as $error) {
                            $this->add('notice', current($error), ['class' => 'warning']);
                        }
                    }

                    $this->flash();
                }
            }
        );
    }

    /**
     * @param       $bag
     * @param       $message
     * @param array $attributes
     *
     * @return MessageManager
     * @throws \Exception
     */
    public function add($bag, $message, $attributes = [])
    {
        $this->assertValidBag($bag);;

        $this->messageBags->put(
            $bag,
            $this->messageBags->get($bag, new Collection)->push(array_merge(['message' => $message], $attributes))
        );

        return $this;
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->messageBags;
    }

    public function flash()
    {
        $this->session->flash('__bags', $this->messageBags->all());

        return $this;
    }

    public function flush()
    {
        $bags = $this->session->get('__bags', new Collection);

        $this->session->forget('__bags');

        return $bags;
    }

    protected function assertValidBag($bag)
    {
        if ( ! $this->isValidBag($bag)) {
            throw new \Exception("Invalid message bag [{$bag}]");
        }
    }

    /**
     * @param $bag
     *
     * @return bool
     */
    protected function isValidBag($bag)
    {
        return in_array($bag, self::$bags);
    }
}
