<?php

namespace App\Listeners\Internal;

use App\Events\Internal\WalkedOut;

class WalkedOutListener
{
    public function handle(WalkedOut $event)
    {
        foreach ($event->sqm->stack as $item) {
            if ($action = config('ragnoria.actions.walk-out')[$item->id] ?? null) {
                $action::handle($event, $item);
            }
        }
    }
}
