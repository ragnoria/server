# Ragnoria server

## Client-Server communication

The data exchange format is identical on both sides: server expects and emits websocket messages in json format with the following
structure:
```json
{
    "event": "foo",
    "params": {
        "param1": "value1",
        "param2": "value2"
    }
}
```

## Events & Listeners:

Events in application are divided into 'internal events' and 'websocket events'. Internal events can not be triggered through websocket message.

#### Events

- Internal events are located in `/app/Events/Internal`.
- Websocket events are located in `/app/Events/Websockets`.
- WebSocket event classes must be added to list of supported websocket events in `/config/ragnoria.php` under `events` key.

#### Listeners:

- Internal listeners are located in `/app/Listeners/Internal`.
- Websocket listeners are located in `/app/Listeners/Websockets`.
- Event-Listener mappings are located in `EventServiceProvider` in `$listen` property.

## In-game commands:

All in-game commands are handled by own class located in `/app/Classes/Commands`.
Commands are assigned to application in `/config/ragnoria.php` under `commands` key.


## In-game actions:

All in-game actions are handled by own class located in `/app/Classes/Actions`.
Actions are assigned to application in `/config/ragnoria.php` under `actions` key in a following structure:
```php
'{action-type}' => [
    '{item-id}' => '{handling-class}'
]
```

_Example action:_
```php
'actions' => [
    'walk-on' => [
        5 => \App\Classes\Actions\OnWalkPoisonField::class
    ],
]
```
Such notation means: when any creature `walk on` item with id `5` server will trigger handling method in `OnWalkPoisonField` class.

Types of actions available so far:
- use
- walk-on
- walk-out
- throw-on
