<?php

namespace App\Listeners\Websockets;

use App\Events\Websockets\Walk;

class WalkListener
{
    public function handle(Walk $event)
    {
        $event->player->walk($event->params['direction']);
    }

}
