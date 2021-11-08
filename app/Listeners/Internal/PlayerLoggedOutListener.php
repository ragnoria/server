<?php

namespace App\Listeners\Internal;

use App\Classes\Client\Effects;
use App\Classes\Client\Events;
use App\Classes\World;
use App\Events\Internal\PlayerLoggedOut;

class PlayerLoggedOutListener
{
    public function handle(PlayerLoggedOut $event)
    {
        foreach (World::getNearbyPlayers($event->player->getSQM()) as $player) {
            $player->sendEvent(Events::RUN_EFFECT, [
                'effect' => Effects::POOF,
                'x' => $event->player->x,
                'y' => $event->player->y,
                'z' => $event->player->z
            ]);
            $player->sendEvent(Events::PLAYER_REMOVE, [
                'playerId' => $event->player->id,
            ]);
        }
    }
}
