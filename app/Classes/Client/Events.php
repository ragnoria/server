<?php

namespace App\Classes\Client;

class Events
{
    const
        AUTH = 'auth',
        PONG = 'pong',
        LOG = 'log',
        CONFIRM_STEP = 'confirm-step',
        RUN_EFFECT = 'run-effect',
        UPDATE_SQM = 'update-sqm',
        SEND_MESSAGE = 'send-message',
        UPDATE_POSITION = 'update-position',
        PLAYER_MOVE = 'player-move',
        PLAYER_REMOVE = 'player-remove',
        PLAYER_ROTATE = 'player-rotate',
        PLAYER_HURT = 'player-hurt';
}
