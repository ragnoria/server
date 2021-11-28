<?php

namespace App\Listeners\Internal;

use App\Classes\Client\Events;
use App\Classes\Item;
use App\Classes\Log;
use App\Classes\World;
use App\Events\Internal\PlayerDie;
use App\Events\Internal\WalkedOut;

class PlayerDieListener
{
    public function handle(PlayerDie $event)
    {
        // add corpse
        $event->player->getSQM()->addItem(new Item(11, 1));
        foreach (World::getNearbyPlayers($event->player->getSQM()) as $player) {
            $player->sendEvent(Events::UPDATE_SQM, [
                'x' => $event->player->x,
                'y' => $event->player->y,
                'z' => $event->player->z,
                'stack' => $event->player->getSQM()->stack,
            ]);
        }

        $event->player->sendEvent(Events::DEAD);

        World::$players->detach($event->player);

        foreach (World::getNearbyPlayers($event->player->getSQM()) as $player) {
            $player->sendEvent(Events::PLAYER_REMOVE, [
                'playerId' => $event->player->id,
            ]);
        }

        event(new WalkedOut($event->player, $event->player->getSQM()));

        $event->player->conn->player = null;
        $event->player->conn->close();
        $event->player->save();

        $event->player->hp = $event->player->hp_max;
        $event->player->x = config('ragnoria.respawn.x');
        $event->player->y = config('ragnoria.respawn.y');
        $event->player->z = config('ragnoria.respawn.z');
        $event->player->save();

        Log::info("Player '{$event->player->name}' died. Players online: " . World::$players->count() . ".");
    }
}
