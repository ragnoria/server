<?php

namespace App\Listeners\Internal;

use App\Classes\Client\Events;
use App\Classes\Helper;
use App\Classes\World;
use App\Events\Internal\PlayerWalk;
use App\Events\Internal\WalkedIn;
use App\Events\Internal\WalkedOut;

class PlayerWalkListener
{
    public function handle(PlayerWalk $event)
    {
        $fromSQM = $event->player->getSQM();
        $toSQM = Helper::getSQMAfterMove($event->player->getSQM(), $event->direction);

        if (!$toSQM || !$toSQM->isWalkable()) {
            $event->player->sendEvent(Events::UPDATE_POSITION, [
                'status' => false,
                'area' => null,
                'x' => $event->player->x,
                'y' => $event->player->y,
                'z' => $event->player->z,
                'direction' => $event->player->direction,
            ]);
            return;
        }

        $event->player->x = $toSQM->x;
        $event->player->y = $toSQM->y;
        $event->player->z = $toSQM->z;
        $event->player->direction = $event->direction;

        $playersOnAreaBeforeStep = World::getNearbyPlayers($fromSQM);
        $playersStillOnArea = [];
        foreach (World::getNearbyPlayers($toSQM) as $player) if ($player !== $event->player) {
            $player->sendEvent(Events::PLAYER_MOVE, [
                'player' => $event->player->toArray(),
                'direction' => $fromSQM->z == $event->player->z ? $event->player->direction : null
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

        $event->player->sendEvent(Events::CONFIRM_STEP, [
            'status' => true,
            'area' => $event->player->getArea(),
            'x' => $event->player->x,
            'y' => $event->player->y,
            'z' => $event->player->z,
            'direction' => $event->player->direction,
        ]);

        event(new WalkedOut($event->player, $fromSQM));
        event(new WalkedIn($event->player, $toSQM));
    }

}
