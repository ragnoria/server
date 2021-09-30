<?php

namespace App\Traits;

use App\Classes\Helper;
use App\Classes\SQM;
use App\Classes\World;
use App\Events\Internal\PlayerTeleported;
use App\Events\Internal\PlayerWalked;
use App\Events\Internal\WalkedIn;
use App\Events\Internal\WalkedOut;
use App\Models\Player;

trait CreatureTrait
{

    public function getSQM(): SQM
    {
        return World::getSQM($this->x, $this->y, $this->z);
    }

    public function walk(string $direction): void
    {
        $fromSQM = $this->getSQM();
        $toSQM = Helper::getSQMAfterStep($this->getSQM(), $direction);

        if ($toSQM && $toSQM->isWalkable()) {
            $this->x = $toSQM->x;
            $this->y = $toSQM->y;
            $this->z = $toSQM->z;
            $this->direction = $direction;
            event(new WalkedOut($this, $fromSQM));
            event(new WalkedIn($this, $toSQM));
        }

        if ($this instanceof Player) {
            event(new PlayerWalked($this, $fromSQM, $toSQM));
        }
    }

    public function teleport(SQM $toSQM): void
    {
        if (!$toSQM->hasGround()) {
            return;
        }

        $fromSQM = $this->getSQM();
        $this->x = $toSQM->x;
        $this->y = $toSQM->y;
        $this->z = $toSQM->z;
        event(new WalkedOut($this, $fromSQM));
        event(new WalkedIn($this, $toSQM));

        if ($this instanceof Player) {
            event(new PlayerTeleported($this, $fromSQM, $toSQM));
        }
    }

}
