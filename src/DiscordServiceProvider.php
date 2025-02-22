<?php

namespace Mountrix\DiscordNotification;

use Illuminate\Support\ServiceProvider;

class DiscordServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DiscordNotifier::class, function ($app): DiscordNotifier {
            return new DiscordNotifier($app);
        });
    }
}
