<?php namespace PortOneFive\Essentials\Messaging;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class MessagingServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['messages'] = $this->app->share(
            function (Application $app) {
                return new MessageManager($app['session.store']);
            }
        );
    }
}
