<?php

namespace App\Services;

use App\Classes\Client\Events;
use App\Classes\World;
use App\Models\Player;
use Ratchet\ConnectionInterface;

class ConnectionService
{
    public static function authorize(ConnectionInterface $conn): bool
    {
        $newPlayerConfig = [
            'role' => Player::ROLE_PLAYER,
            'name' => 'Guest #' . Player::count(),
            'x' => 5000,
            'y' => 5000,
            'z' => 0,
        ];
        $player = new Player($newPlayerConfig);
        $player->save();

        /** @var Player $player */
        $player = Player::find($player->id);
        $player->conn = $conn;
        $player->conn->player = $player;
        $player->sendEvent(Events::AUTH, [
            'state' => 'pass',
            'params' => [
                'hero' => $player->toArray(),
                'area' => $player->getArea(),
                'players' => World::getNearbyPlayers($player->getSQM())
            ]
        ]);

        return true;
    }

}
