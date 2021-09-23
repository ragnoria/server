<?php

namespace App\Listeners;

use App\Classes\Client\Events;
use App\Events\Walk;

class WalkListener
{
    public function handle(Walk $event)
    {
        $moved = $event->player->move($event->params['direction']);

        $event->player->sendEvent(Events::MOVEMENT_CONFIRM_STEP, [
            'status' => $moved ? 'success' : 'failed',
            'area' => $moved ? $event->player->getArea() : null,
            'players' => $moved ? [] : null,
            'x' => $event->player->x,
            'y' => $event->player->y,
            'z' => $event->player->z,
            'direction' => $event->player->direction,
        ]);
    }

}
