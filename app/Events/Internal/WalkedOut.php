<?php

namespace App\Events\Internal;

use App\Classes\SQM;
use App\Interfaces\CreatureInterface;
use Illuminate\Foundation\Events\Dispatchable;

class WalkedOut
{
    use Dispatchable;


    public CreatureInterface $creature;

    public SQM $sqm;


    public function __construct(CreatureInterface $creature, SQM $sqm)
    {
        $this->creature = $creature;
        $this->sqm = $sqm;
    }

}
