<?php

namespace App\Listeners\Websockets;

use App\Classes\Helper;
use App\Classes\World;
use App\Events\Websockets\UseItem;
use App\Models\Player;

class UseItemListener
{
    public function handle(UseItem $event)
    {
        list($x, $y, $z) = $event->params['pos'];
        if (!$sqm = World::getSQM($x, $y, $z)) {
            return;
        }

        if ($event->player->hasPermissions(Player::ROLE_GAMEMASTER) === false) {
            if (Helper::inRange($event->player, $sqm) === false) {
                return;
            }
        }

        if(!$item = end($sqm->stack)) {
            return;
        }

        if ($action = config('ragnoria.actions.use')[$item->id] ?? null) {
            $action::handle($event, $item);
        }
    }

}
