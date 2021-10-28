<?php

namespace App\Classes;

class Outfit
{
    public int $base;

    public int $head;

    public int $body;

    public int $back;

    public int $hands;

    public string $hair;

    public string $primary;

    public string $secondary;

    public string $details;


    public function __construct()
    {
        $this->base = 1;
        $this->head = rand(1,3);
        $this->body = rand(0,2);
        $this->back = rand(0,3);
        $this->hands = rand(0,1);
        $this->hair = Helper::randomColor();
        $this->primary = Helper::randomColor();
        $this->secondary = Helper::randomColor();
        $this->details = Helper::randomColor();
    }

    public function toArray(): array
    {
        return [
            'base' => $this->base,
            'head' => $this->head,
            'body' => $this->body,
            'back' => $this->back,
            'hands' => $this->hands,
            'hair' => $this->hair,
            'primary' => $this->primary,
            'secondary' => $this->secondary,
            'details' => $this->details,
        ];
    }

}
