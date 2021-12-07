<?php

namespace App\Models;

class State
{
    public const
        BURNING = 1,
        POISONED = 2;


    private array $ticks = [];


    public function is(int $state): bool
    {
        return !!$this->getTicks($state);
    }

    public function tick(int $state): void
    {
        if (isset($this->ticks[$state])) {
            $this->ticks[$state]--;
            if ($this->ticks[$state] <= 0) {
                unset($this->ticks[$state]);
            }
        }
    }

    public function setTicks(int $state, int $amount): void
    {
        $this->ticks[$state] = $amount;
    }

    public function addTicks(int $state, int $amount): void
    {
        if (isset($this->ticks[$state])) {
            $this->ticks[$state] += $amount;
        } else {
            $this->ticks[$state] = $amount;
        }
    }

    public function getTicks(int $state): int
    {
        return $this->ticks[$state] ?? 0;
    }

}
