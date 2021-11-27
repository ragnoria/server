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

        if ($player->role == Player::ROLE_GAMEMASTER) {
            $player->speed = 40;
            $player->outfit->head = 2;
            $player->outfit->body = 1;
            $player->outfit->back = 3;
            $player->outfit->hands = 0;
            $player->outfit->hair = '#3d3d3d';
            $player->outfit->primary = '#ffc100';
            $player->outfit->secondary = '#ff0000';
            $player->outfit->details = '#ffc100';
        }
    }

}
