<?php

namespace App\Classes;

use App\Models\Item;

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

    public function addItem(Item $item, bool $onBottom = false): void
    {
        if ($onBottom) {
            array_unshift($this->stack, $item);
        } else {
            $this->stack[] = $item;
        }
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
        if ($this->isBlockingCreatures()) {
            return false;
        }
        foreach (World::$players as $player) if ($player->getSQM() === $this) {
            return false;
        }

        return true;
    }

    public function isBlockingProjectiles(): bool
    {
        foreach ($this->stack as $item) {
            if ($item->isBlockingProjectiles()) {
                return true;
            }
        }

        return false;
    }

    public function isBlockingCreatures(): bool
    {
        foreach ($this->stack as $item) {
            if ($item->isBlockingCreatures()) {
                return true;
            }
        }

        return false;
    }

    public function isBlockingItems(): bool
    {
        foreach ($this->stack as $item) {
            if ($item->isBlockingItems()) {
                return true;
            }
        }

        return false;
    }

}
