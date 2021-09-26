<?php

namespace App\Events\Internal;

use App\Classes\SQM;
use App\Models\Player;
use Illuminate\Foundation\Events\Dispatchable;

class PlayerTeleported
{
    use Dispatchable;


    public Player $player;

    public SQM $fromSQM;

    public SQM $toSQM;


    public function __construct(Player $player, SQM $fromSQM, SQM $toSQM)
    {
        $this->player = $player;
        $this->fromSQM = $fromSQM;
        $this->toSQM = $toSQM;
    }

}
