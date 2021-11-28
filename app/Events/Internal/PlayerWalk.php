<?php

namespace App\Events\Internal;

use App\Models\Player;
use Illuminate\Foundation\Events\Dispatchable;

class PlayerWalk
{
    use Dispatchable;


    public Player $player;

    public string $direction;


    public function __construct(Player $player, string $direction)
    {
        $this->player = $player;
        $this->direction = $direction;
    }

}
