<?php

namespace App\Console\Commands;

use App\Classes\ItemStructureCollection;
use App\Classes\Log;
use App\Classes\World;
use App\Http\Controllers\WebSocketController;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class StartRagnoriaServer extends Command
{
    protected $signature = 'ragnoria:serve';

    protected $description = 'Start ragnoria server';
    

    public function handle()
    {
        $host = env('WEBSOCKETS_HOST');
        $port = env('WEBSOCKETS_PORT');

        if (is_resource($fp = @fsockopen($host, $port))) {
            fclose($fp);
            Log::error("Socket $host:$port is already open!");
            exit();
        }

        ItemStructureCollection::init();
        World::init();
        app()->instance(IoServer::class, IoServer::factory(
            new HttpServer(
                new WsServer(
                    new WebSocketController()
                )
            ),
            $port
        ));

        Log::success('Server started!');

        app()->get(IoServer::class)->run();
    }

}
