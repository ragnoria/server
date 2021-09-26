<?php

namespace App\Listeners\Internal;

use App\Classes\Client\Effects;
use App\Classes\Client\Events;
use App\Classes\World;
use App\Events\Internal\PlayerLoggedIn;

class PlayerLoggedInListener
{
    public function handle(PlayerLoggedIn $event)
    {
        foreach (World::getNearbyPlayers($event->player->getSQM()) as $player) {
            $player->sendEvent(Events::RUN_EFFECT, [
                'effect' => Effects::LOGIN,
                'x' => $event->player->x,
                'y' => $event->player->y,
                'z' => $event->player->z
            ]);
            if ($player !== $event->player) {
                $player->sendEvent(Events::MOVE_PLAYER, [
                    'player' => $event->player->toArray(),
                    'direction' => null
                ]);
            }
        }
    }
}
