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
        $player = new Player([
            'role' => Player::ROLE_PLAYER,
            'name' => 'Guest #' . (Player::count() + 1),
            'hp' => 100,
            'hp_max' => 100,
            'x' => config('ragnoria.respawn.x'),
            'y' => config('ragnoria.respawn.y'),
            'z' => config('ragnoria.respawn.z'),
            'ip' => $conn->remoteAddress,
        ]);
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
