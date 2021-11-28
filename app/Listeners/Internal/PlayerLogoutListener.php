<?php

namespace App\Listeners\Internal;

use App\Classes\Client\Effects;
use App\Classes\Client\Events;
use App\Classes\Log;
use App\Classes\World;
use App\Events\Internal\PlayerLogout;
use App\Events\Internal\WalkedOut;

class PlayerLogoutListener
{
    public function handle(PlayerLogout $event)
    {
        World::$players->detach($event->player);

        foreach (World::getNearbyPlayers($event->player->getSQM()) as $player) {
            $player->sendEvent(Events::RUN_EFFECT, [
                'id' => Effects::POOF,
                'x' => $event->player->x,
                'y' => $event->player->y,
                'z' => $event->player->z
            ]);
            $player->sendEvent(Events::PLAYER_REMOVE, [
                'playerId' => $event->player->id,
            ]);
        }

        event(new WalkedOut($event->player, $event->player->getSQM()));

        $event->player->conn->close();
        $event->player->save();

        Log::info("Player '{$event->player->name}' logged out. Players online: " . World::$players->count() . ".");
    }
}
