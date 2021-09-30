<?php

namespace App\Listeners\Internal;

use App\Events\Internal\WalkedIn;

class WalkedInListener
{
    public function handle(WalkedIn $event)
    {
        foreach ($event->sqm->stack as $item) {
            if ($action = config('ragnoria.actions.walk-on')[$item->id] ?? null) {
                $action::handle($event, $item);
            }
        }
    }
}
