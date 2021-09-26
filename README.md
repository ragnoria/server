# Server

### Running server:

```bash
php artisan ragnoria:serve
```

### Events & listeners:

- Create event class in `/app/Events`
- Create listener class in `/app/Listeners`
- Add listener mapping in `EventServiceProvider::$listen`

### In-game commands:

- All in-game commands are handled by own class located in `/app/Classes/Commands`.
- Command class must be added to commands list in `/config/ragnoria.php`.
