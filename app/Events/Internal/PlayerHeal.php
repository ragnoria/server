<?php

namespace App\Events\Internal;

use App\Models\Player;
use Illuminate\Foundation\Events\Dispatchable;

class PlayerHeal
{
    use Dispatchable;


    public Player $player;

    public int $power;

    public int $effect;


    public function __construct(Player $player, int $power, int $effect)
    {
        $this->player = $player;
        $this->power = $power;
        $this->effect = $effect;
    }

}
