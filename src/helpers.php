<?php

use Illuminate\Contracts\Auth\Access\Gate;

if ( ! function_exists('visitor')) {
    /**
     * @param null                 $key
     *
     * @var \Illuminate\Auth\Guard $auth
     * @return \PortOneFive\Essentials\Users\User|false
     */
     function visitor($key = null)
    {
        $auth = app('auth');

        if ( ! $auth->check()) {
            return false;
        }

        return $key == null ? $auth->user() : object_get($auth->user(), $key);
    }
}

if ( ! function_exists('can')) {
    /**
     * @var \Illuminate\Auth\Guard $auth
     *
     * @param array                $arguments
     *
     * @return false|\PortOneFive\Essentials\Users\User
     */
    function can($ability, $arguments = [])
    {
        if (is_string($ability)) {
            list($ability, $arguments) = [$ability, $arguments];
        } else {
            list($ability, $arguments) = [debug_backtrace(false, 3)[2]['function'], $ability];
        }

        return app(Gate::class)->check($ability, $arguments);
    }
}

if ( ! function_exists('percentage')) {
    function percentage($amount, $total)
    {
        return number_format($total == 0 ? 0 : $amount / $total * 100, 0);
    }
}

if ( ! function_exists('messages')) {
    /**
     * @return \PortOneFive\Essentials\Messaging\MessageManager
     */
    function messages()
    {
        return app('messages');
    }
}
if ( ! function_exists('success')) {
    /**
     * @param        $message
     * @param string $class
     * @param int    $timeOut
     *
     * @return \PortOneFive\Essentials\Messaging\MessageManager
     */
    function success($message, $class = 'success', $timeOut = 2000)
    {
        return messages()->success($message, $class, $timeOut);
    }
}

if ( ! function_exists('notify')) {
    /**
     * @param        $message
     * @param string $class
     * @param int    $timeOut
     *
     * @return \PortOneFive\Essentials\Messaging\MessageManager
     */
    function notify($message, $class = 'info', $timeOut = 5000)
    {
        return messages()->notify($message, $class, $timeOut);
    }
}

if ( ! function_exists('error')) {
    /**
     * @param        $message
     * @param string $title
     * @param string $class
     *
     * @return \PortOneFive\Essentials\Messaging\MessageManager
     */
    function error($message, $title = 'The following error occurred', $class = 'error')
    {
        return messages()->error($message, $title, $class);
    }
}

if ( ! function_exists('overlay')) {
    /**
     * @param        $message
     * @param        $title
     * @param string $class
     *
     * @return \PortOneFive\Essentials\Messaging\MessageManager
     */
    function overlay($message, $title, $class = 'info')
    {
        return messages()->overlay($message, $title, $class);
    }
}
