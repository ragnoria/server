<?php

return [

    'area' => [
        'width' => 31,
        'height' => 17,
    ],

    'events' => [
        'ping' => \App\Events\Ping::class,
        'push' => \App\Events\Push::class,
        'rotate' => \App\Events\Rotate::class,
        'say' => \App\Events\Say::class,
        'walk' => \App\Events\Walk::class,
    ],

    'commands' => [
        \App\Classes\Commands\StraightTeleport::class,
    ],

];
