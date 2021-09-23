<?php

namespace App\Classes;


class SQM
{
    public int $x;

    public int $y;

    public int $z;

    /** @var Item[] */
    public array $stack = [];


    public function __construct(int $x, int $y, int $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function addItem(Item $item): void
    {
        $this->stack[] = $item;
    }

    public function hasGround(): bool
    {
        foreach ($this->stack as $item) {
            if ($item->getType() == Item::TYPE_GROUND) {
                return true;
            }
        }

        return false;
    }

    public function isWalkable(): bool
    {
        if (!$this->hasGround()) {
            return false;
        }

        return true;
    }

}
