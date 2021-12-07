<?php

namespace App\Classes;

use Ratchet\Server\IoServer;
use React\EventLoop\TimerInterface;

/**
 * Alias (static proxy/accessor) for app()->get(IoServer::class)->loop;
 *
 * @method static TimerInterface addTimer(int $interval, callable $callback)
 * @method static TimerInterface addPeriodicTimer(int $interval, callable $callback)
 * @method static void cancelTimer(TimerInterface $timer)
 */
abstract class Loop
{
    public static function __callStatic($name, $arguments)
    {
        return app()->get(IoServer::class)->loop->$name(...$arguments);
    }
}
