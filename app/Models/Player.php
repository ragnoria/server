<?php

namespace App\Models;

use App\Classes\World;
use App\Classes\WsEventRequest;
use App\Events\Internal\PlayerLoggedIn;
use App\Events\Internal\PlayerLoggedOut;
use App\Interfaces\CreatureInterface;
use App\Traits\CreatureTrait;
use Illuminate\Database\Eloquent\Model;
use Ratchet\ConnectionInterface;

/**
 * DB
 * @property int $id
 * @property int $role
 * @property string $name
 * @property int $x
 * @property int $y
 * @property int $z
 * @property string $created_at
 * @property string $updated_at
 */
class Player extends Model implements CreatureInterface
{
    use CreatureTrait;


    public const
        ROLE_PLAYER = 0,
        ROLE_GAMEMASTER = 1;


    public ConnectionInterface $conn;

    public string $direction;

    public int $speed;


    protected $guarded = [];


    public function login(): void
    {
        World::$players->attach($this);
        event(new PlayerLoggedIn($this));
    }

    public function logout(): void
    {
        World::$players->detach($this);
        $this->save();
        event(new PlayerLoggedOut($this));
    }

    public function hasPermissions($role): bool
    {
        return $this->role >= $role;
    }

    public function getArea(): array
    {
        $factor_x = ceil(config('ragnoria.area.width') / 2) - 1;
        $factor_y = ceil(config('ragnoria.area.height') / 2) - 1;
        $sqm_range_x = range(($this->x - $factor_x), ($this->x + $factor_x));
        $sqm_range_y = range(($this->y - $factor_y), ($this->y + $factor_y));

        $area = [];
        $levels = $this->z >= 0 ? [0, 1, 2, 3] : [-3, -2, -1];
        foreach ($levels as $z) {
            foreach ($sqm_range_y as $y) {
                $y = $y + ($z - $this->z);
                foreach ($sqm_range_x as $x) {
                    $x = $x + ($z - $this->z);
                    $area[$z][$y][$x] = World::getSQM($x, $y, $z) ? World::getSQM($x, $y, $z)->stack : [];
                }
            }
        }

        return $area;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            'direction' => $this->direction,
            'speed' => $this->speed,
            'outfit' => $this->outfit->toArray(),
        ];
    }

    public function sendEvent(string $event, array $data = []): void
    {
        $this->conn->send(new WsEventRequest($event, $data));
    }

}
