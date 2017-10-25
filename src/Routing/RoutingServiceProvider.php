<?php

namespace PortOneFive\Essentials\Routing;

use Illuminate\Routing\RoutingServiceProvider as ServiceProvider;

class RoutingServiceProvider extends ServiceProvider
{
    protected function registerRouter()
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });
    }

}