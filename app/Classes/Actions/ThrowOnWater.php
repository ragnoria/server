<?php

namespace App\Classes\Actions;

use App\Classes\Client\Effects;
use App\Classes\Client\Events;
use App\Classes\Item;
use App\Classes\World;
use App\Events\Websockets\Push;

class ThrowOnWater
{
    public static function handle(Push $event, Item $item)
    {
        list($x, $y, $z) = $event->params['toPos'];
        if (!$toSQM = World::getSQM($x, $y, $z)) {
            return;
        }

        array_pop($toSQM->stack);
        foreach(World::getNearbyPlayers($toSQM) as $player) {
            $player->sendEvent(Events::RUN_EFFECT, [
                'id' => Effects::WATER,
                'x' => $toSQM->x,
                'y' => $toSQM->y,
                'z' => $toSQM->z
            ]);
        }
    }
}
