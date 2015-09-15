<?php namespace PortOneFive\Essentials\Messaging;

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
        $this->app->bindShared(
            'messages',
            function () {
                return $this->app->make(MessageManager::class);
            }
        );
    }
}
