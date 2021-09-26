<?php

namespace App\Services;

class MessageParserService
{
    /**
     * @param string $msg
     * @return string
     */
    public static function getEvent(string $msg): string
    {
        $msg = json_decode($msg, true);
        $event = $msg['event'] ?? null;

        if (!is_string($event) || preg_match('/[^a-z_\-0-9]/i', $event)) {
            throw new \InvalidArgumentException('The command cannot contain special characters.');
        }

        $event = strtolower(trim($event));
        if (!$eventClass = config('ragnoria.events')[$event] ?? null) {
            throw new \InvalidArgumentException("'{$event}' is not recognized as an internal event.");
        }

        return $eventClass;
    }

    /**
     * @param string $msg
     * @return array
     */
    public static function getParams(string $msg): array
    {
        $msg = json_decode($msg, true);
        $params = $msg['params'] ?? [];

        if (!is_array($params)) {
            throw new \InvalidArgumentException('Invalid parameters.');
        }

        return $params;
    }

}
