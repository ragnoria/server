<?php

namespace App\Providers;

use App\Events\Ping;
use App\Events\Push;
use App\Events\Rotate;
use App\Events\Say;
use App\Events\Walk;
use App\Listeners\PingListener;
use App\Listeners\PushListener;
use App\Listeners\RotateListener;
use App\Listeners\SayListener;
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
        Ping::class => [PingListener::class],
        Walk::class => [WalkListener::class],
        Push::class => [PushListener::class],
        Rotate::class => [RotateListener::class],
        Say::class => [SayListener::class],
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
