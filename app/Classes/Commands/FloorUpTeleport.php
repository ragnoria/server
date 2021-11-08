<?php

namespace App\Classes\Commands;

use App\Classes\World;
use App\Interfaces\CommandInterface;
use App\Models\Player;

class FloorUpTeleport implements CommandInterface
{
    public static string $signature = '/up';

    public static int $role = Player::ROLE_GAMEMASTER;


    public static function cast(Player $player, array $params): void
    {
        if ($toSQM = World::getSQM($player->x, $player->y, $player->z + 1)) {
            $player->teleport($toSQM);
        }
    }

}
