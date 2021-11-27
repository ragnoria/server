<?php

namespace App\Listeners\Internal;

use App\Classes\Client\Effects;
use App\Classes\Client\Events;
use App\Classes\World;
use App\Events\Internal\PlayerTeleported;

class PlayerTeleportedListener
{
    public function handle(PlayerTeleported $event)
    {
        $playersOnAreaBeforeStep = World::getNearbyPlayers($event->fromSQM);
        $playersStillOnArea = [];
        foreach (World::getNearbyPlayers($event->toSQM) as $player) {
            if ($player !== $event->player) {
                $player->sendEvent(Events::PLAYER_MOVE, [
                    'player' => $event->player->toArray(),
                    'direction' => null
                ]);
                if (!in_array($player, $playersOnAreaBeforeStep)) {
                    $event->player->sendEvent(Events::PLAYER_MOVE, [
                        'player' => $player->toArray(),
                        'direction' => null
                    ]);
                }
                $playersStillOnArea[] = $player;
            }
            $player->sendEvent(Events::RUN_EFFECT, [
                'id' => Effects::TELEPORT,
                'x' => $event->player->x,
                'y' => $event->player->y,
                'z' => $event->player->z
            ]);
        }

        foreach ($playersOnAreaBeforeStep as $player) if ($player !== $event->player) {
            if (!in_array($player, $playersStillOnArea)) {
                $player->sendEvent(Events::PLAYER_REMOVE, [
                    'playerId' => $event->player->id
                ]);
                $event->player->sendEvent(Events::PLAYER_REMOVE, [
                    'playerId' => $player->id
                ]);
            }
        }

        $event->player->sendEvent(Events::UPDATE_POSITION, [
            'status' => true,
            'area' => $event->player->getArea(),
            'x' => $event->player->x,
            'y' => $event->player->y,
            'z' => $event->player->z,
            'direction' => $event->player->direction,
        ]);
    }
}
