<?php
use PortOneFive\Essentials\Messaging\MessageManager;

if ( ! function_exists('visitor')) {
    /**
     * @param null                 $key
     *
     * @var \Illuminate\Auth\Guard $auth
     * @return \Illuminate\Auth\Authenticatable|bool|mixed
     */
    function visitor($key = null)
    {
        static $auth;

        if ( ! $auth) {
            $auth = app('auth');
        }

        if ( ! $auth->check()) {
            return false;
        }

        return $key == null ? $auth->user() : object_get($auth->user(), $key);
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
     * @return MessageManager
     */
    function messages()
    {
        return app('messages');
    }
}
if ( ! function_exists('alert')) {
    /**
     * @param        $message
     * @param string $class
     * @param int    $timeOut
     *
     * @return MessageManager
     */
    function alert($message, $class = 'success', $timeOut = 2000)
    {
        return messages()->add('alert', $message, ['class' => $class, 'timeOut' => $timeOut]);
    }
}

if ( ! function_exists('notify')) {
    /**
     * @param        $message
     * @param string $class
     * @param int    $timeOut
     *
     * @return MessageManager
     */
    function notify($message, $class = 'info', $timeOut = 5000)
    {
        return messages()->add('notice', $message, ['class' => $class, 'timeOut' => $timeOut]);
    }
}

if ( ! function_exists('error')) {
    /**
     * @param        $message
     * @param        $title
     *
     * @return MessageManager
     */
    function error($message, $title = 'The following error occurred')
    {
        return messages()->add('error', $message, ['title' => $title]);
    }
}

if ( ! function_exists('message')) {
    /**
     * @param        $message
     * @param        $title
     * @param string $class
     *
     * @return MessageManager
     */
    function message($message, $title, $class = 'info')
    {
        return messages()->add('message', $message, ['title' => $title, 'class' => $class]);
    }
}