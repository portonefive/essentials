<?php namespace PortOneFive\Essentials\Messaging;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Session\Store;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse;

/**
 * Class MessageManager
 *
 * @package PortOneFive\Essentials\Messaging
 */
class MessageManager
{

    /**
     * The session writer.
     *
     * @var Store
     */
    private $session;
    /**
     * @var Application
     */
    private $app;
    /**
     * @var Collection
     */
    private $messages;

    /**
     * Create a new flash notifier instance.
     *
     * @param Store       $session
     * @param Application $app
     */
    function __construct(Store $session, Application $app)
    {
        $this->session = $session;
        $this->app     = $app;

        $this->messages = $this->session->pull('__messages', new Collection());

        if ($this->session->has('errors')) {
            $errors = $this->session->get('errors')->getBag('default')->getMessages();
            foreach ($errors as $error) {
                $this->add(['message' => current($error), 'type' => 'info', 'class' => 'warning'], false);
            }
        }
    }

    public function info($message, $class = 'info', $timeOut = 400)
    {
        $this->add(['type' => __FUNCTION__, 'message' => $message, 'class' => $class, 'timeOut' => $timeOut]);

        return $this;
    }

    public function success($message, $class = 'success', $timeOut = 400)
    {
        $this->add(['type' => __FUNCTION__, 'message' => $message, 'class' => $class, 'timeOut' => $timeOut]);

        return $this;
    }

    public function error($message, $title = '', $class = 'error')
    {
        $this->add(['type' => __FUNCTION__, 'message' => $message, 'title' => $title, 'class' => $class]);

        return $this;
    }

    public function warning($message, $class = 'warning', $timeOut = 400)
    {
        $this->add(['type' => __FUNCTION__, 'message' => $message, 'class' => $class, 'timeOut' => $timeOut]);

        return $this;
    }

    public function overlay($message, $title = '', $class = 'warning')
    {
        $this->add(['type' => __FUNCTION__, 'message' => $message, 'class' => $class, 'title' => $title]);

        return $this;
    }

    public function add(array $message, $flash = true)
    {
        $this->messages->push($message);

        if ($flash) {
            $this->session->flash('__messages', $this->messages);
        }

        return $this;
    }

    public function any()
    {
        return ! $this->messages->isEmpty();
    }

    public function all()
    {
        return $this->messages->all();
    }
}
