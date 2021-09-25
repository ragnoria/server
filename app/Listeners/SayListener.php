<?php

namespace App\Listeners;

use App\Classes\Client\Events;
use App\Classes\World;
use App\Events\Say;

class SayListener
{
    public function handle(Say $event)
    {
        $message = trim($event->params['message']);
        if (strlen($message) === 0 || strlen($message) > 255) {
            return;
        }

        foreach (World::getPlayersAround($event->player->getSQM()) as $player) {
            $player->sendEvent(Events::SEND_MESSAGE, [
                'message' => $message,
                'player' => $event->player->toArray(),
            ]);
        }
    }
}
