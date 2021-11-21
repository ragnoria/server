<?php

namespace App\Listeners\Websockets;

use App\Classes\Client\Events;
use App\Classes\Helper;
use App\Classes\SQM;
use App\Classes\World;
use App\Events\Websockets\Push;
use App\Models\Player;

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

        if ($event->player->hasPermissions(Player::ROLE_GAMEMASTER) === false) {
            if (Helper::inRange($event->player, $fromSQM) === false) {
                return;
            }
        }

        if (!$this->canPush($fromSQM, $toSQM)) {
            return;
        }

        $toSQMinitialStack = $toSQM->stack;
        $toSQM->addItem(array_pop($fromSQM->stack));

        foreach ($toSQMinitialStack as $item) {
            if ($action = config('ragnoria.actions.throw-on')[$item->id] ?? null) {
                $action::handle($event, $item);
            }
        }

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
