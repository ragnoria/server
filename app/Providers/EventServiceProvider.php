<?php

namespace App\Providers;

use App\Events\Ping;
use App\Events\Walk;
use App\Listeners\PingListener;
use App\Listeners\WalkListener;
use App\Models\Player;
use App\Observers\PlayerObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Ping::class => [
            PingListener::class,
        ],
        Walk::class => [
            WalkListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Player::observe(PlayerObserver::class);
    }
}
