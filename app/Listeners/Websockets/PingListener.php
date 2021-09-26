<?php

namespace App\Listeners\Websockets;

use App\Classes\Client\Events;
use App\Events\Websockets\Ping;

class PingListener
{
    public function handle(Ping $event)
    {
        $event->player->sendEvent(Events::PONG, [
            'requestId' => $event->params['requestId']
        ]);
    }
}
