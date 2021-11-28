<?php

namespace App\Events\Internal;

use App\Models\Player;
use Illuminate\Foundation\Events\Dispatchable;

class PlayerDie
{
    use Dispatchable;


    public Player $player;


    public function __construct(Player $player)
    {
        $this->player = $player;
    }

}
