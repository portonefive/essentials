<?php

namespace PortOneFive\Essentials\Foundation;

use Illuminate\Events\EventServiceProvider;
use Illuminate\Foundation\Application as BaseApplication;
use PortOneFive\Essentials\Routing\RoutingServiceProvider;

class Application extends BaseApplication
{
    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new EventServiceProvider($this));

        $this->register(new RoutingServiceProvider($this));
    }
}