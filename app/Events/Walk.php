<?php

namespace App\Events;

use App\Models\Player;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Ratchet\ConnectionInterface;

class Walk
{
    use Dispatchable, InteractsWithSockets;

    public ConnectionInterface $conn;

    public Player $player;

    public array $params = [
        'direction' => null,
    ];


    public function __construct(ConnectionInterface $conn, array $params)
    {
        $this->conn = $conn;
        $this->player = $conn->player;
        $this->params = $params;
    }

}
