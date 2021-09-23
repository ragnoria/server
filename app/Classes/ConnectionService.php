<?php

namespace App\Classes;

use App\Classes\Client\Events;
use App\Models\Player;
use Ratchet\ConnectionInterface;


class ConnectionService
{
    public static function authorize(ConnectionInterface $conn): bool
    {
        $newPlayerConfig = [
            'name' => 'Guest #' . Player::count(),
            'x' => 5000,
            'y' => 5000,
            'z' => 0,
        ];
        $player = new Player($newPlayerConfig);
        $player->save();

        $player = Player::find($player->id);
        $player->conn = $conn;
        $player->conn->player = $player;
        $player->sendEvent(Events::AUTH, [
            'state' => 'pass',
            'params' => [
                'hero' => [
                    'name' => $player->name,
                    'x' => $player->x,
                    'y' => $player->y,
                    'z' => $player->z,
                    'speed' => $player->speed,
                    'direction' => $player->direction,
                ],
                'area' => $player->getArea(),
                'players' => []
            ]
        ]);

        return true;
    }

}
