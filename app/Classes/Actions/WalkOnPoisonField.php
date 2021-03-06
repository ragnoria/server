<?php

namespace App\Classes\Actions;

use App\Classes\Client\Effects;
use App\Classes\Loop;
use App\Classes\World;
use App\Events\Internal\WalkedIn;
use App\Models\Item;
use App\Models\State;
use React\EventLoop\TimerInterface;

class WalkOnPoisonField
{
    public static function handle(WalkedIn $event, Item $item)
    {
        if (!$event->creature->state->is(State::POISONED)) {
            Loop::addPeriodicTimer(4, function (TimerInterface $timer) use ($event) {
                if (!World::$players->contains($event->creature)) {
                    Loop::cancelTimer($timer);
                    return;
                }
                if ($event->creature->state->is(State::POISONED)) {
                    $event->creature->hurt($event->creature->state->getTicks(State::POISONED), Effects::POISON);
                    $event->creature->state->tick(State::POISONED);
                }
                if (!$event->creature->state->is(State::POISONED)) {
                    Loop::cancelTimer($timer);
                }
                $event->creature->sendUpdateStatus();
            });
        }

        $event->creature->state->setTicks(State::POISONED, 9);
        $event->creature->hurt(10, Effects::POISON);
    }
}
