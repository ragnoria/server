<?php

namespace App\Listeners\Internal;

use App\Classes\Client\Events;
use App\Classes\World;
use App\Events\Internal\PlayerWalked;

class PlayerWalkedListener
{
    public function handle(PlayerWalked $event)
    {
        $moved = $event->fromSQM !== $event->player->getSQM();

        if ($moved) {
            $playersOnAreaBeforeStep = World::getNearbyPlayers($event->fromSQM);
            $playersStillOnArea = [];
            foreach (World::getNearbyPlayers($event->toSQM) as $player) if ($player !== $event->player) {
                $player->sendEvent(Events::PLAYER_MOVE, [
                    'player' => $event->player->toArray(),
                    'direction' => $event->fromSQM->z == $event->player->z ? $event->player->direction : null
                ]);
                if (!in_array($player, $playersOnAreaBeforeStep)) {
                    $event->player->sendEvent(Events::PLAYER_MOVE, [
                        'player' => $player->toArray(),
                        'direction' => null
                    ]);
                }
                $playersStillOnArea[] = $player;
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
        }

        $event->player->sendEvent(Events::CONFIRM_STEP, [
            'status' => $moved ? 'success' : 'failed',
            'area' => $moved ? $event->player->getArea() : null,
            'x' => $event->player->x,
            'y' => $event->player->y,
            'z' => $event->player->z,
            'direction' => $event->player->direction,
        ]);
    }

}
