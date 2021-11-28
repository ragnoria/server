<?php

namespace App\Events\Internal;

use App\Classes\SQM;
use App\Models\Player;
use Illuminate\Foundation\Events\Dispatchable;

class PlayerTeleport
{
    use Dispatchable;


    public Player $player;

    public SQM $toSQM;


    public function __construct(Player $player, SQM $toSQM)
    {
        $this->player = $player;
        $this->toSQM = $toSQM;
    }

}
