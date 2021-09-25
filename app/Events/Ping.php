<?php

namespace App\Events;

use App\Models\Player;
use Illuminate\Foundation\Events\Dispatchable;
use Ratchet\ConnectionInterface;

class Ping
{
    use Dispatchable;


    public Player $player;

    public array $params = [
        /** @var string */
        'requestId' => null,
    ];


    public function __construct(ConnectionInterface $conn, array $params)
    {
        $this->player = $conn->player;
        $this->params = $params;
    }

}
