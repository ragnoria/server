<?php

namespace App\Listeners;

use App\Classes\Client\Events;
use App\Classes\World;
use App\Events\Walk;

class WalkListener
{
    public function handle(Walk $event)
    {
        $playersOnAreaBeforeStep = World::getPlayersAround($event->player->getSQM());
        $fromSQM = $event->player->getSQM();
        $moved = $event->player->move($event->params['direction']);

        $event->player->sendEvent(Events::CONFIRM_STEP, [
            'status' => $moved ? 'success' : 'failed',
            'area' => $moved ? $event->player->getArea() : null,
            'players' => $moved ? [] : null,
            'x' => $event->player->x,
            'y' => $event->player->y,
            'z' => $event->player->z,
            'direction' => $event->player->direction,
        ]);

        if (!$moved) {
            return;
        }

        $playersStillOnArea = [];
        foreach (World::getPlayersAround($event->player->getSQM()) as $player) if ($player !== $event->player) {
            $player->sendEvent(Events::MOVE_PLAYER, [
                'player' => $event->player->toArray(),
                'direction' => $fromSQM->z == $event->player->z ? $event->params['direction'] : null
            ]);
            $playersStillOnArea[] = $player;
        }

        foreach ($playersOnAreaBeforeStep as $player) if ($event->player !== $player) {
            if (!in_array($player, $playersStillOnArea)) {
                $player->sendEvent(Events::REMOVE_PLAYER, [
                    'playerId' => $event->player->id
                ]);
                $event->player->sendEvent(Events::REMOVE_PLAYER, [
                    'playerId' => $player->id
                ]);
            }
        }
    }

}
