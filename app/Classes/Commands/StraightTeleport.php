<?php

namespace App\Classes\Commands;

use App\Classes\World;
use App\Models\Player;

class StraightTeleport
{
    public static string $signature = '/a';

    public static int $role = Player::ROLE_GAMEMASTER;


    public static function cast(Player $player, array $params)
    {
        $distance = $params[0] ?? null;

        if (!$distance || !is_numeric($distance) || (int)$distance != $distance || (int)$distance > 99 || (int)$distance <= 0) {
            return;
        }

        $distance = (int)$distance;
        switch ($player->direction) {
            case 'South':
                $targetSQM = World::getSQM($player->x, $player->y + $distance, $player->z);
                break;
            case 'East':
                $targetSQM = World::getSQM($player->x + $distance, $player->y, $player->z);
                break;
            case 'North':
                $targetSQM = World::getSQM($player->x, $player->y - $distance, $player->z);
                break;
            case 'West':
                $targetSQM = World::getSQM($player->x - $distance, $player->y, $player->z);
                break;
        }
        
        if (!empty($targetSQM)) {
            $player->teleport($targetSQM);
        }
    }

}
