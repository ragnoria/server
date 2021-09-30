<?php

return [

    'area' => [
        'width' => 31,
        'height' => 17,
    ],

    'respawn' => [
        'x' => 5000,
        'y' => 5000,
        'z' => 0
    ],

    'events' => [
        'ping' => \App\Events\Websockets\Ping::class,
        'push' => \App\Events\Websockets\Push::class,
        'rotate' => \App\Events\Websockets\Rotate::class,
        'say' => \App\Events\Websockets\Say::class,
        'walk' => \App\Events\Websockets\Walk::class,
        'useitem' => \App\Events\Websockets\UseItem::class,
    ],

    'commands' => [
        \App\Classes\Commands\StraightTeleport::class,
        \App\Classes\Commands\FloorUpTeleport::class,
        \App\Classes\Commands\FloorDownTeleport::class,
        \App\Classes\Commands\TownTeleport::class,
    ],

    'actions' => [
        'walk-on' => [
            5 => \App\Classes\Actions\OnWalkPoisonField::class
        ],
        'use' => [
            7 => \App\Classes\Actions\OnUseStreetLamp::class,
            8 => \App\Classes\Actions\OnUseStreetLamp::class
        ],
    ]

];
