<?php

namespace App\Listeners;

use App\Events\Walk;

class WalkListener
{
    public function handle(Walk $event)
    {
        $event->player->move($event->params['direction']);
    }

}
