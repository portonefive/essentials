<?php namespace PortOneFive\Essentials\Http\Controllers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Session\Store;
use PortOneFive\Essentials\MessageBag;
use PortOneFive\Essentials\Messaging\SendsMessages;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests, SendsMessages;

    protected static $booted = false;

    /** @var Container */
    protected static $app;

    /** @var Store */
    protected static $session;

    /** @var Request */
    protected static $request;

    public static function boot(Container $app, Store $session, Request $request)
    {
        if (self::$booted) {
            return;
        }

        self::$booted = true;

        self::setApp($app);
        self::setSession($session);
        self::setRequest($request);
    }

    /**
     * @return Request
     */
    protected static function request()
    {
        return self::$request;
    }

    /**
     * @param Request $request
     */
    public static function setRequest(Request $request)
    {
        self::$request = $request;
    }

    /**
     * @return Store
     */
    protected static function session()
    {
        return self::$session;
    }

    /**
     * @param Store $session
     */
    public static function setSession(Store $session)
    {
        self::$session = $session;
    }

    /**
     * @param       $class
     * @param       $method
     * @param array $arguments
     *
     * @return mixed
     */
    protected function controllerReroute($class, $method, array $arguments = [])
    {
        $controller = $this->app()->make($class);

        return $this->app()->call([$controller, $method], $arguments);
    }

    /**
     * @return Container
     */
    protected static function app()
    {
        return self::$app;
    }

    /**
     * @param Container $app
     */
    public static function setApp(Container $app)
    {
        self::$app = $app;
    }
}
