<?php

namespace App\Listeners;

use App\Classes\Client\Events;
use App\Classes\Log;
use App\Classes\World;
use App\Events\Say;

class SayListener
{
    private Say $event;

    private string $message;


    public function handle(Say $event)
    {
        $this->event = $event;
        $this->message = trim($event->params['message']);

        if (strlen($this->message) === 0 || strlen($this->message) > 255) {
            return;
        }

        if ($this->message[0] === '/') {
            $this->handleCommand();
        } else {
            $this->handleMessage();
        }
    }


    private function handleMessage()
    {
        foreach (World::getPlayersAround($this->event->player->getSQM()) as $player) {
            $player->sendEvent(Events::SEND_MESSAGE, [
                'message' => $this->message,
                'player' => $this->event->player->toArray(),
            ]);
        }
    }

    private function handleCommand()
    {
        $params = [];
        foreach(explode(' ', $this->message) as $i => $partial) {
            $partial = trim($partial);
            $i === 0 ? $signature = $partial : $params[] = $partial;
        }
        if (!isset($signature)) {
            return;
        }

        foreach (config('ragnoria.commands') as $cmdClass) {
            if ($signature === $cmdClass::$signature) {
                $cmdClass::cast($this->event->player, $params);
                return;
            }
        }

        Log::info('Player ' . $this->event->player->name . ' trying to call unknown command: ' . $signature . ' with params: ' .json_encode($params));
    }

}
