<?php

namespace PortOneFive\Essentials\Foundation;

use Illuminate\Events\EventServiceProvider;
use PortOneFive\Essentials\Routing\RoutingServiceProvider;

class Application extends \Illuminate\Foundation\Application
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