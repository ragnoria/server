<?php

namespace App\Classes\Actions;

use App\Classes\Client\Events;
use App\Classes\World;
use App\Events\Websockets\UseItem;
use App\Models\Item;

class UseStreetLamp
{
    public static function handle(UseItem $event, Item $item)
    {
        list($x, $y, $z) = $event->params['pos'];
        if (!$sqm = World::getSQM($x, $y, $z)) {
            return;
        }

        if ($item->id == 7) {
            array_pop($sqm->stack);
            $sqm->addItem(new Item(8, 1));
        }

        if ($item->id == 8) {
            array_pop($sqm->stack);
            $sqm->addItem(new Item(7, 1));
        }

        foreach (World::getNearbyPlayers($sqm) as $player) {
            $player->sendEvent(Events::UPDATE_SQM, [
                'x' => $sqm->x,
                'y' => $sqm->y,
                'z' => $sqm->z,
                'stack' => $sqm->stack,
            ]);
        }

    }
}
