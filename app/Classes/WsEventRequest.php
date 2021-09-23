<?php

namespace App\Classes;


class WsEventRequest
{
    protected string $event;

    protected array $params = [];


    public function __construct(string $event, array $params = [])
    {
        $this->event = $event;
        $this->params = $params;
    }

    public function __toString(): string
    {
        return json_encode([
            'event' => $this->event,
            'params' => $this->params,
        ]);
    }

}
