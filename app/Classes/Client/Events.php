<?php

namespace App\Classes\Client;

class Events
{
    const
        AUTH = 'App.authorization',
        PONG = 'Libs_UI.Ping.pull',
        LOG = 'Libs_Console.addLog',
        CONFIRM_STEP = 'Libs_Movement.confirmStep',
        RUN_EFFECT = 'Libs_Effect.run',
        UPDATE_SQM = 'Libs_Board.updateSQM',
        ROTATE_PLAYER = 'Libs_Player.rotate',
        SEND_MESSAGE = 'Libs_Chat.prepareMessage',
        MOVE_PLAYER = 'Libs_Player.move',
        REMOVE_PLAYER = 'Libs_Player.remove';
}
