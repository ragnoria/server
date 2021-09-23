#Server

### Running server:

```bash
php artisan ragnoria:serve
```


### Adding events & listeners:

- Create event class in `/app/Events`
- Create listener class in `/app/Listeners`
- Add listener mapping in `EventServiceProvider::$listen`

