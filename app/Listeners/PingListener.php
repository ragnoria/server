<?php

namespace App\Listeners;

use App\Classes\Client\Events;
use App\Events\Ping;

class PingListener
{
    public function handle(Ping $event)
    {
        $event->player->sendEvent(Events::PONG, [
            'requestId' => $event->params['requestId']
        ]);
    }
}
