<?php

namespace App\Classes\Actions;

use App\Classes\Client\Effects;
use App\Classes\Item;
use App\Events\Internal\WalkedIn;

class OnWalkPoisonField
{
    public static function handle(WalkedIn $event, Item $item)
    {
        $event->creature->hurt(10, Effects::POISON);
    }
}
