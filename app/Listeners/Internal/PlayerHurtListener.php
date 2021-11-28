<?php

namespace App\Listeners\Internal;

use App\Classes\Client\Effects;
use App\Classes\Client\Events;
use App\Classes\World;
use App\Events\Internal\PlayerHurt;
use App\Models\Player;

class PlayerHurtListener
{
    public function handle(PlayerHurt $event)
    {
        if ($event->player->hasPermissions(Player::ROLE_GAMEMASTER)) {
            $event->power = 0;
        }

        if ($event->power > 0) {
            if ($event->player->hp <= $event->power) {
                $event->power = $event->player->hp;
            }

            $event->player->hp -= $event->power;

            foreach (World::getNearbyPlayers($event->player->getSQM()) as $player) {
                $player->sendEvent(Events::PLAYER_HURT, [
                    'player_id' => $event->player->id,
                    'power' => $event->power,
                    'effect' => $event->effect,
                    'hp' => $event->player->hp,
                    'hp_max' => $event->player->hp_max,
                ]);
            }
        } else {
            foreach (World::getNearbyPlayers($event->player->getSQM()) as $player) {
                $player->sendEvent(Events::RUN_EFFECT, [
                    'id' => Effects::POOF,
                    'x' => $event->player->x,
                    'y' => $event->player->y,
                    'z' => $event->player->z
                ]);
            }
        }

        if ($event->player->hp === 0) {
            $event->player->die();
        }
    }
}
