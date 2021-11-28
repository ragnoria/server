<?php

namespace App\Listeners\Internal;

use App\Events\Internal\PlayerHeal;

class PlayerHealListener
{
    public function handle(PlayerHeal $event)
    {
        if ($event->player->hp_max <= $event->player->hp + $event->power) {
            $event->player->hp = $event->player->hp_max;
        } else {
            $event->player->hp += $event->power;
        }
    }
}
