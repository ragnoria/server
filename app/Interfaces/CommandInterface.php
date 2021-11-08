<?php

namespace App\Interfaces;

use App\Models\Player;

interface CommandInterface
{
    public static function cast(Player $player, array $params): void;
}
