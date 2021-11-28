<?php

namespace App\Providers;

use App\Events\Internal\PlayerDie;
use App\Events\Internal\PlayerHeal;
use App\Events\Internal\PlayerHurt;
use App\Events\Internal\PlayerLogin;
use App\Events\Internal\PlayerLogout;
use App\Events\Internal\PlayerTeleport;
use App\Events\Internal\PlayerWalk;
use App\Events\Internal\WalkedIn;
use App\Events\Internal\WalkedOut;
use App\Events\Websockets\Ping;
use App\Events\Websockets\Push;
use App\Events\Websockets\Rotate;
use App\Events\Websockets\Say;
use App\Events\Websockets\UseItem;
use App\Events\Websockets\Walk;
use App\Listeners\Internal\PlayerDieListener;
use App\Listeners\Internal\PlayerHealListener;
use App\Listeners\Internal\PlayerHurtListener;
use App\Listeners\Internal\PlayerLoginListener;
use App\Listeners\Internal\PlayerLogoutListener;
use App\Listeners\Internal\PlayerTeleportListener;
use App\Listeners\Internal\PlayerWalkListener;
use App\Listeners\Internal\WalkedInListener;
use App\Listeners\Internal\WalkedOutListener;
use App\Listeners\Websockets\PingListener;
use App\Listeners\Websockets\PushListener;
use App\Listeners\Websockets\RotateListener;
use App\Listeners\Websockets\SayListener;
use App\Listeners\Websockets\UseItemListener;
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
        Push::class => [PushListener::class],
        Rotate::class => [RotateListener::class],
        Say::class => [SayListener::class],
        Walk::class => [WalkListener::class],
        UseItem::class => [UseItemListener::class],

        // internal
        PlayerLogin::class => [PlayerLoginListener::class],
        PlayerLogout::class => [PlayerLogoutListener::class],
        PlayerHurt::class => [PlayerHurtListener::class],
        PlayerHeal::class => [PlayerHealListener::class],
        PlayerDie::class => [PlayerDieListener::class],
        PlayerTeleport::class => [PlayerTeleportListener::class],
        PlayerWalk::class => [PlayerWalkListener::class],
        WalkedIn::class => [WalkedInListener::class],
        WalkedOut::class => [WalkedOutListener::class],
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
