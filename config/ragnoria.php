<?php

return [

    'area' => [
        'width' => 31,
        'height' => 17,
    ],

    'events' => [
        'ping' => \App\Events\Websockets\Ping::class,
        'push' => \App\Events\Websockets\Push::class,
        'rotate' => \App\Events\Websockets\Rotate::class,
        'say' => \App\Events\Websockets\Say::class,
        'walk' => \App\Events\Websockets\Walk::class,
    ],

    'commands' => [
        \App\Classes\Commands\StraightTeleport::class,
    ],

];
