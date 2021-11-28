<?php

namespace App\Models;

use App\Classes\Client\Effects;
use App\Classes\Outfit;
use App\Classes\SQM;
use App\Classes\World;
use App\Classes\WsEventRequest;
use App\Events\Internal\PlayerDie;
use App\Events\Internal\PlayerHeal;
use App\Events\Internal\PlayerHurt;
use App\Events\Internal\PlayerLogin;
use App\Events\Internal\PlayerLogout;
use App\Events\Internal\PlayerTeleport;
use App\Events\Internal\PlayerWalk;
use App\Interfaces\CreatureInterface;
use Illuminate\Database\Eloquent\Model;
use Ratchet\ConnectionInterface;

/**
 * DB
 * @property int $id
 * @property int $role
 * @property string $name
 * @property int $hp
 * @property int $hp_max
 * @property int $x
 * @property int $y
 * @property int $z
 * @property string $token
 * @property string $ip
 *
 * @property string $created_at
 * @property string $updated_at
 */
class Player extends Model implements CreatureInterface
{
    public const
        ROLE_PLAYER = 0,
        ROLE_GAMEMASTER = 1;


    public ConnectionInterface $conn;

    public Outfit $outfit;

    public string $direction;

    public int $speed;


    protected $guarded = [];


    public function login(): void
    {
        event(new PlayerLogin($this));
    }

    public function logout(): void
    {
        event(new PlayerLogout($this));
    }

    public function walk(string $direction): void
    {
        event(new PlayerWalk($this, $direction));
    }

    public function teleport(SQM $toSQM): void
    {
        event(new PlayerTeleport($this, $toSQM));
    }

    public function hurt(int $power, int $effect = Effects::BLOOD): void
    {
        event(new PlayerHurt($this, $power, $effect));
    }

    public function heal(int $power, int $effect = null): void
    {
        event(new PlayerHeal($this, $power, $effect));
    }

    public function die(): void
    {
        event(new PlayerDie($this));
    }

    public function hasPermissions($role): bool
    {
        return $this->role >= $role;
    }

    public function getSQM(): SQM
    {
        return World::getSQM($this->x, $this->y, $this->z);
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
            'role' => $this->role,
            'name' => $this->name,
            'hp' => $this->hp,
            'hp_max' => $this->hp_max,
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
