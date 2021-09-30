<?php

namespace App\Classes\Actions;

use App\Classes\Client\Effects;
use App\Classes\Client\Events;
use App\Classes\Item;
use App\Classes\World;
use App\Events\Internal\WalkedIn;

class OnWalkPoisonField
{
    public static function handle(WalkedIn $event, Item $item)
    {
        foreach(World::getNearbyPlayers($event->sqm) as $player) {
            $player->sendEvent(Events::RUN_EFFECT, [
                'effect' => Effects::POISON,
                'x' => $event->creature->x,
                'y' => $event->creature->y,
                'z' => $event->creature->z
            ]);
        }
    }
}
