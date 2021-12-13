<?php

namespace App\Classes\Actions;

use App\Classes\Client\Effects;
use App\Classes\Loop;
use App\Events\Internal\WalkedIn;
use App\Models\Item;
use App\Models\State;
use React\EventLoop\TimerInterface;

class WalkOnFireField
{
    public static function handle(WalkedIn $event, Item $item)
    {
        if (!$event->creature->state->is(State::BURNING)) {
            Loop::addPeriodicTimer(1, function (TimerInterface $timer) use ($event) {
                if ($event->creature->state->is(State::BURNING)) {
                    $event->creature->hurt(2, Effects::FIRE);
                    $event->creature->state->tick(State::BURNING);
                }
                if (!$event->creature->state->is(State::BURNING)) {
                    Loop::cancelTimer($timer);
                }
                $event->creature->sendUpdateStatus();
            });
        }

        $event->creature->state->setTicks(State::BURNING, 10);
        $event->creature->hurt(20, Effects::FIRE);
    }
}
