<?php

namespace App\Listeners\Websockets;

use App\Classes\Client\Events;
use App\Classes\SQM;
use App\Classes\World;
use App\Events\Websockets\Push;

class PushListener
{
    public function handle(Push $event)
    {
        list($x, $y, $z) = $event->params['fromPos'];
        if (!$fromSQM = World::getSQM($x, $y, $z)) {
            return;
        }

        list($x, $y, $z) = $event->params['toPos'];
        if (!$toSQM = World::getSQM($x, $y, $z)) {
            return;
        }

        if (abs($fromSQM->x - $event->player->x) > 1 || abs($fromSQM->y - $event->player->y) > 1) {
            return;
        }

        if ($this->canPush($fromSQM, $toSQM)) {
            $toSQM->addItem(array_pop($fromSQM->stack));

            foreach (World::getNearbyPlayers($fromSQM) as $player) {
                $player->sendEvent(Events::UPDATE_SQM, [
                    'x' => $fromSQM->x,
                    'y' => $fromSQM->y,
                    'z' => $fromSQM->z,
                    'stack' => $fromSQM->stack,
                ]);
            }

            foreach (World::getNearbyPlayers($toSQM) as $player) {
                $player->sendEvent(Events::UPDATE_SQM, [
                    'x' => $toSQM->x,
                    'y' => $toSQM->y,
                    'z' => $toSQM->z,
                    'stack' => $toSQM->stack,
                ]);
            }
        }
    }

    private function canPush(SQM $fromSQM, SQM $toSQM): bool
    {
        if (empty($fromSQM->stack)) {
            return false;
        }

        if (!end($fromSQM->stack)->isMoveable()) {
            return false;
        }

        if (!$toSQM->hasGround()) {
            return false;
        }

        if ($toSQM->isBlockingItems()) {
            return false;
        }

        foreach (World::getSQMsBetween($fromSQM, $toSQM) as $sqm) {
            if ($sqm->isBlockingProjectiles()) {
                return false;
            }
        }

        return true;
    }

}
