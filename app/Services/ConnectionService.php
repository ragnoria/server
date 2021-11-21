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
        parse_str($conn->httpRequest->getUri()->getQuery(), $queryParams);
        $token = $queryParams['token'] ?? null;

        if ($token && $player = Player::where('token', $token)->first()) {

            // case when player is already logged in: close old connection and override it with current
            /** @var Player $splPlayer */
            foreach (World::$players as $splPlayer) if ($splPlayer->id == $player->id) {
                $splPlayer->conn->player = null;
                $splPlayer->conn->close();
                $splPlayer->conn = $conn;
                $splPlayer->update(['token' => uniqid("", true)]);
                $conn->player = $splPlayer;
                self::sendPass($splPlayer);

                return true;
            }

        } else {
            $player = new Player([
                'role' => Player::ROLE_PLAYER,
                'name' => 'Guest #' . (Player::count() + 1),
                'hp' => 200,
                'hp_max' => 200,
                'x' => config('ragnoria.respawn.x'),
                'y' => config('ragnoria.respawn.y'),
                'z' => config('ragnoria.respawn.z'),
                'ip' => $conn->remoteAddress
            ]);
        }

        $player->token = uniqid("", true);
        $player->save();

        /** @var Player $player */
        $player = Player::find($player->id);
        $player->conn = $conn;
        $player->conn->player = $player;

        self::sendPass($player);

        $player->login();

        return true;
    }


    private static function sendPass(Player $player): void
    {
        $player->sendEvent(Events::AUTH, [
            'state' => 'pass',
            'token' => $player->token,
            'params' => [
                'hero' => $player->toArray(),
                'area' => $player->getArea(),
                'players' => array_filter(World::getNearbyPlayers($player->getSQM()), fn($p) => $p->id != $player->id)
            ]
        ]);
    }

}
