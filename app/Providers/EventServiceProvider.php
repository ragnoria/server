<?php

namespace App\Providers;

use App\Events\Internal\PlayerLoggedIn;
use App\Events\Internal\PlayerLoggedOut;
use App\Events\Internal\PlayerTeleported;
use App\Events\Internal\PlayerWalked;
use App\Events\Websockets\Ping;
use App\Events\Websockets\Push;
use App\Events\Websockets\Rotate;
use App\Events\Websockets\Say;
use App\Events\Websockets\Walk;
use App\Listeners\Internal\PlayerLoggedInListener;
use App\Listeners\Internal\PlayerLoggedOutListener;
use App\Listeners\Internal\PlayerTeleportedListener;
use App\Listeners\Internal\PlayerWalkedListener;
use App\Listeners\Websockets\PingListener;
use App\Listeners\Websockets\PushListener;
use App\Listeners\Websockets\RotateListener;
use App\Listeners\Websockets\SayListener;
use App\Listeners\Websockets\WalkListener;
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
        // websocket
        Ping::class => [PingListener::class],
        Walk::class => [WalkListener::class],
        Push::class => [PushListener::class],
        Rotate::class => [RotateListener::class],
        Say::class => [SayListener::class],

        // internal
        PlayerTeleported::class => [PlayerTeleportedListener::class],
        PlayerWalked::class => [PlayerWalkedListener::class],
        PlayerLoggedIn::class => [PlayerLoggedInListener::class],
        PlayerLoggedOut::class => [PlayerLoggedOutListener::class],
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
