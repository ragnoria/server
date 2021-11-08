<?php

namespace App\Classes\Commands;

use App\Classes\World;
use App\Interfaces\CommandInterface;
use App\Models\Player;

class TownTeleport implements CommandInterface
{
    public static string $signature = '/t';

    public static int $role = Player::ROLE_GAMEMASTER;


    public static function cast(Player $player, array $params): void
    {
        if ($toSQM = World::getSQM(config('ragnoria.respawn.x'), config('ragnoria.respawn.y'), config('ragnoria.respawn.z'))) {
            $player->teleport($toSQM);
        }
    }

}
