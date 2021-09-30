<?php

namespace App\Interfaces;


use App\Classes\SQM;

interface CreatureInterface
{
    public function getSQM(): SQM;

    public function walk(string $direction): void;

    public function teleport(SQM $toSQM): void;
}
