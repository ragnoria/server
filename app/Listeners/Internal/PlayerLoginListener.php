<?php

namespace App\Listeners\Internal;

use App\Classes\Client\Effects;
use App\Classes\Client\Events;
use App\Classes\Log;
use App\Classes\World;
use App\Events\Internal\PlayerLogin;
use App\Events\Internal\WalkedIn;

class PlayerLoginListener
{
    public function handle(PlayerLogin $event)
    {
        World::$players->attach($event->player);

        foreach (World::getNearbyPlayers($event->player->getSQM()) as $player) {
            $player->sendEvent(Events::RUN_EFFECT, [
                'id' => Effects::TELEPORT,
                'x' => $event->player->x,
                'y' => $event->player->y,
                'z' => $event->player->z
            ]);
            if ($player !== $event->player) {
                $player->sendEvent(Events::PLAYER_MOVE, [
                    'player' => $event->player->toArray(),
                    'direction' => null
                ]);
            }
        }

        event(new WalkedIn($event->player, $event->player->getSQM()));

        Log::info("Player '{$event->player->name}' logged in. Players online: " . World::$players->count() . ".");
    }
}
