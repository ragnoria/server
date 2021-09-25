<?php

namespace App\Http\Controllers;

use App\Classes\Client\Events;
use App\Classes\ConnectionService;
use App\Classes\Log;
use App\Classes\MessageParser;
use App\Classes\World;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class WebSocketController extends Controller implements MessageComponentInterface
{

    public function onOpen(ConnectionInterface $conn)
    {
        if (ConnectionService::authorize($conn)) {
            $conn->player->login();
            Log::info("Player '{$conn->player->name}' logged in. Players online: " . World::$players->count() . ".");
        } else {
            $conn->close();
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        if (!empty($conn->player)) {
            $conn->player->logout();
            Log::info("Player '{$conn->player->name}' logged out. Players online: " . World::$players->count() . ".");
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        Log::warning($e->getMessage());

        if (!empty($conn->player)) {
            $conn->player->logout();
            Log::info("Player '{$conn->player->name}' logged out. Players online: " . World::$players->count() . ".");
        }

        $conn->close();
    }

    public function onMessage(ConnectionInterface $conn, $msg)
    {
        if (empty($conn->player)) {
            return;
        }

        try {
            $event = MessageParser::getEvent($msg);
            $params = MessageParser::getParams($msg);
            event(new $event($conn, $params));
        } catch (\InvalidArgumentException $e) {
            Log::warning($e->getMessage());
            $conn->player->sendEvent(Events::LOG, [
                'msg' => $e->getMessage(),
                'level' => 'default'
            ]);
        } catch (\Throwable $t) {
            Log::warning(sprintf("Player '%s' tried to call '%s' with params '%s'. Caught exception: '%s' in '%s:%s'",
                $conn->player ? $conn->player->name : '?',
                $event ?? '?',
                json_encode($params ?? '?'),
                $t->getMessage(),
                $t->getFile(),
                $t->getLine()
            ));
            $conn->player->sendEvent(Events::LOG, [
                'msg' => 'Invalid command syntax.',
                'level' => 'default'
            ]);
        }
    }

}
