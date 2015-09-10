<?php namespace PortOneFive\Essentials;

use ABU\Routing\Router;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use PortOneFive\Essentials\Facades\Form;
use PortOneFive\Essentials\Facades\HTML;
use PortOneFive\Essentials\Http\Controllers\Controller;
use PortOneFive\Essentials\Messaging\MessagingServiceProvider;

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

                if (method_exists($controller, 'ready')) {
                    $app->call([$controller, 'ready']);
                }

                if (method_exists($controller, 'protect')) {
                    $app->call([$controller, 'protect']);
                }
            }
        );

        AliasLoader::getInstance(['HTML' => HTML::class, 'Form' => Form::class])->register();

        Dictionary::setEventDispatcher($this->app[Dispatcher::class]);

        $this->app->register(new MessagingServiceProvider($this->app));
    }
}
