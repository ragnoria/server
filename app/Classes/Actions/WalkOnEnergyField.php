<?php

namespace App\Classes\Actions;

use App\Classes\Client\Effects;
use App\Events\Internal\WalkedIn;
use App\Models\Item;

class WalkOnEnergyField
{
    public static function handle(WalkedIn $event, Item $item)
    {
        $event->creature->hurt(25, Effects::ENERGY);
    }
}
