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
        SEND_MESSAGE = 'Libs_Chat.prepareMessage',
        UPDATE_POSITION = 'Libs_Movement.updatePosition',
        PLAYER_MOVE = 'Libs_Player.move',
        PLAYER_REMOVE = 'Libs_Player.remove',
        PLAYER_ROTATE = 'Libs_Player.rotate',
        PLAYER_HURT = 'Libs_Player.hurt';
}
