<?php

namespace App\Classes\Client;


class Events
{
    const
        AUTH = 'App.authorization',
        PONG = 'Libs_UI.Ping.pull',
        CONSOLE_ADD_LOG = 'Libs_Console.addLog',
        MOVEMENT_CONFIRM_STEP = 'Libs_Movement.confirmStep',
        EFFECT_RUN = 'Libs_Effect.run';
}
