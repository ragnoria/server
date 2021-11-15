<?php

namespace App\Classes\Actions;

use App\Classes\Client\Effects;
use App\Classes\Item;
use App\Events\Internal\WalkedIn;

class WalkOnEnergyField
{
    public static function handle(WalkedIn $event, Item $item)
    {
        $event->creature->hurt(25, Effects::ENERGY);
    }
}
