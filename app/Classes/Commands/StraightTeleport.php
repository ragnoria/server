<?php

namespace App\Classes\Commands;

use App\Classes\Helper;
use App\Interfaces\CommandInterface;
use App\Models\Player;

class StraightTeleport implements CommandInterface
{
    public static string $signature = '/a';

    public static int $role = Player::ROLE_GAMEMASTER;


    public static function cast(Player $player, array $params): void
    {
        $distance = $params[0] ?? null;

        if (!$distance || !is_numeric($distance) || (int)$distance != $distance || (int)$distance > 99 || (int)$distance <= 0) {
            return;
        }

        if (!$targetSQM = Helper::getSQMAfterMove($player->getSQM(), $player->direction, $distance)) {
            return;
        }

        $player->teleport($targetSQM);
    }

}
