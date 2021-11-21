<?php

return [

    'area' => [
        'width' => 31,
        'height' => 17,
    ],

    'respawn' => [
        'x' => 5018,
        'y' => 5001,
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
        \App\Classes\Commands\ForwardTeleport::class,
        \App\Classes\Commands\FloorUpTeleport::class,
        \App\Classes\Commands\FloorDownTeleport::class,
        \App\Classes\Commands\TownTeleport::class,
    ],

    'actions' => [
        'use' => [
            7 => \App\Classes\Actions\UseStreetLamp::class,
            8 => \App\Classes\Actions\UseStreetLamp::class
        ],
        'walk-on' => [
            5 => \App\Classes\Actions\WalkOnPoisonField::class,
            9 => \App\Classes\Actions\WalkOnEnergyField::class,
            10 => \App\Classes\Actions\WalkOnFireField::class,
            12 => \App\Classes\Actions\WalkOnTrap::class
        ],
        'walk-out' => [
            13 => \App\Classes\Actions\WalkOutTrap::class,
        ],
        'throw-on' => [
            28 => \App\Classes\Actions\ThrowOnWater::class,
            69 => \App\Classes\Actions\ThrowOnLava::class
         ]
    ]

];
