# Ragnoria server

### Running server:

```bash
php artisan ragnoria:serve
```

### Client-Server communication

The data exchange format is identical on both sides: server expects and emits messages in json format with the following
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

### Events:

We divide events into 'internal events' and 'websocket events'. Internal events can not be triggered through websocket
message.
- All events are located in `/app/Events`
- WebSocket event classes must be added to list of supported websocket events in `/config/ragnoria.php` under `events` key.

### Listeners

- All listeners are located in `/app/Listeners`.
- Event-Listener mappings are located in `EventServiceProvider` in `$listen` property.

### In-game commands:

All in-game commands are handled by own class located in `/app/Classes/Commands`. To work command class must be added to
supported commands list in `/config/ragnoria.php` under `commands` key.
