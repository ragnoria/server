<?php

namespace App\Classes\Actions;

use App\Classes\Client\Events;
use App\Classes\Item;
use App\Classes\World;
use App\Events\Internal\WalkedOut;

class WalkOutTrap
{
    public static function handle(WalkedOut $event, Item $item)
    {
        foreach ($event->sqm->stack as $index => $item) {
            if ($item->id === 13) {
                unset($event->sqm->stack[$index]);
            }
        }
        $event->sqm->addItem(new Item(12, 1), true);

        foreach (World::getNearbyPlayers($event->sqm) as $player) {
            $player->sendEvent(Events::UPDATE_SQM, [
                'x' => $event->sqm->x,
                'y' => $event->sqm->y,
                'z' => $event->sqm->z,
                'stack' => $event->sqm->stack,
            ]);
        }
    }
}
