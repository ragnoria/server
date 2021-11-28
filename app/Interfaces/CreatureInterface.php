<?php

namespace App\Interfaces;

use App\Classes\Client\Effects;
use App\Classes\SQM;

interface CreatureInterface
{
    public function getSQM(): SQM;

    public function walk(string $direction): void;

    public function teleport(SQM $toSQM): void;

    public function heal(int $power): void;

    public function hurt(int $power, int $effect = Effects::FIRE): void;

    public function die();
}
