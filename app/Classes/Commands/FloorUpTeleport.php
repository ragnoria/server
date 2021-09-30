<?php

namespace App\Classes\Commands;

use App\Classes\World;
use App\Models\Player;

class FloorUpTeleport
{
    public static string $signature = '/up';

    public static int $role = Player::ROLE_GAMEMASTER;


    public static function cast(Player $player, array $params)
    {
        if ($toSQM = World::getSQM($player->x, $player->y, $player->z + 1)) {
            $player->teleport($toSQM);
        }
    }

}
