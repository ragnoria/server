<?php

namespace App\Listeners;

use App\Classes\Client\Events;
use App\Classes\Log;
use App\Classes\World;
use App\Events\Rotate;

class RotateListener
{
    public function handle(Rotate $event)
    {
        if ($event->player->direction === $event->params['direction']) {
            return;
        }
        if (!in_array($event->params['direction'], ['North', 'East', 'South', 'West', 'NorthEast', 'NorthWest', 'SouthEast', 'SouthWest'])) {
            Log::info($event->player->name . ' - trying to rotate on direction: ' . json_encode($event->params['direction']));
            return;
        }

        $event->player->direction = $event->params['direction'];

        foreach (World::getNearbyPlayers($event->player->getSQM()) as $player) if ($event->player !== $player) {
            $player->sendEvent(Events::ROTATE_PLAYER, [
                'player' => $event->player->id,
                'direction' => $event->player->direction,
            ]);
        }
    }

}
