<?php

namespace App\Observers;

use App\Classes\Outfit;
use App\Models\Player;


class PlayerObserver
{
    public function retrieved(Player $player)
    {
        $player->direction = 'South';
        $player->speed = 10;
        $player->outfit = new Outfit();
    }

}
