<?php

namespace App\Models;

use App\Classes\ItemStructureCollection;

class Item
{
    public const
        TYPE_GROUND = 1,
        TYPE_EDGE = 2,
        TYPE_BUILDING = 3,
        TYPE_NATURE = 4,
        TYPE_TERIOR = 5,
        TYPE_ORNAMENT = 6,
        TYPE_ACCESSORY = 7,
        TYPE_EQUIPMENT = 8,
        TYPE_OTHER = 9,
        TYPE_CORPSE = 10;

    public int $id;

    public int $quantity;

    public ?int $action_id;

    public ?string $guid;


    public function __construct(int $id, int $quantity, int $action_id = null)
    {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->action_id = $action_id;
        $this->guid = uniqid();
    }

    public function getType(): int
    {
        return ItemStructureCollection::$itemStructures[$this->id]['type'];
    }

    public function getName(): string
    {
        return ItemStructureCollection::$itemStructures[$this->id]['name'];
    }

    public function getAltitude(): int
    {
        return ItemStructureCollection::$itemStructures[$this->id]['altitude'];
    }

    public function isAnimating(): bool
    {
        return ItemStructureCollection::$itemStructures[$this->id]['is_animating'];
    }

    public function isBlockingCreatures(): bool
    {
        return ItemStructureCollection::$itemStructures[$this->id]['is_blocking_creatures'];
    }

    public function isBlockingProjectiles(): bool
    {
        return ItemStructureCollection::$itemStructures[$this->id]['is_blocking_projectiles'];
    }

    public function isBlockingItems(): bool
    {
        return ItemStructureCollection::$itemStructures[$this->id]['is_blocking_items'];
    }

    public function isMoveable(): bool
    {
        return ItemStructureCollection::$itemStructures[$this->id]['is_moveable'];
    }

    public function isPickupable(): bool
    {
        return ItemStructureCollection::$itemStructures[$this->id]['is_pickupable'];
    }

    public function isStackable(): bool
    {
        return ItemStructureCollection::$itemStructures[$this->id]['is_stackable'];
    }

    public function isAlwaysTop(): bool
    {
        return ItemStructureCollection::$itemStructures[$this->id]['is_always_top'];
    }

    public function getLightRadius(): int
    {
        return ItemStructureCollection::$itemStructures[$this->id]['light_radius'];
    }

    public function getLightLevel(): int
    {
        return ItemStructureCollection::$itemStructures[$this->id]['light_level'];
    }

    public function getLightColor(): string
    {
        return ItemStructureCollection::$itemStructures[$this->id]['light_color'];
    }

    public function getPaddingX(): int
    {
        return ItemStructureCollection::$itemStructures[$this->id]['padding_x'];
    }

    public function getPaddingY(): int
    {
        return ItemStructureCollection::$itemStructures[$this->id]['padding_y'];
    }

    public function getSprites(): string
    {
        return ItemStructureCollection::$itemStructures[$this->id]['sprites'];
    }

}
