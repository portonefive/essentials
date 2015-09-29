<?php namespace PortOneFive\Essentials;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use PortOneFive\Essentials\Auth\Access\Gate;
use PortOneFive\Essentials\Html\HtmlServiceProvider;
use PortOneFive\Essentials\Http\Controllers\Controller;
use PortOneFive\Essentials\Messaging\MessagingServiceProvider;
use PortOneFive\Tabulator\TabulatorServiceProvider;

class EssentialsServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__ . '/helpers.php';
        require_once __DIR__ . '/blade.php';

        $this->app->afterResolving(
            Controller::class,
            function (Controller $controller, Application $app) {

                $app->call([$controller, 'boot']);

                if (method_exists($controller, 'ready')) {
                    $app->call([$controller, 'ready']);
                }

                if (method_exists($controller, 'protect')) {
                    $app->call([$controller, 'protect']);
                }
            }
        );

        Dictionary::setEventDispatcher($this->app[Dispatcher::class]);

        $this->app->register(TabulatorServiceProvider::class);
        $this->app->register(MessagingServiceProvider::class);
        $this->app->register(TabulatorServiceProvider::class);
        $this->app->register(HtmlServiceProvider::class);

        $this->app->singleton(GateContract::class, function ($app) {
            return new Gate($app, function () use ($app) { return $app['auth']->user(); });
        });
    }
}
