<?php

namespace App\Observers;

use App\Models\Player;


class PlayerObserver
{
    public function retrieved(Player $player)
    {
        $player->direction = 'South';
        $player->speed = 10;
    }

}
