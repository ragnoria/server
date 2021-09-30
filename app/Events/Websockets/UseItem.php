<?php

namespace App\Events\Websockets;

use App\Models\Player;
use Illuminate\Foundation\Events\Dispatchable;
use Ratchet\ConnectionInterface;

class UseItem
{
    use Dispatchable;


    public Player $player;

    public array $params = [
        /** @var array */
        'pos' => null,
    ];


    public function __construct(ConnectionInterface $conn, array $params)
    {
        $this->player = $conn->player;
        $this->params = $params;
    }

}
