<?php

namespace App\Models;

use App\Classes\SQM;
use App\Classes\World;
use App\Classes\WsEventRequest;
use App\Events\Internal\PlayerLoggedIn;
use App\Events\Internal\PlayerLoggedOut;
use App\Events\Internal\PlayerTeleported;
use App\Events\Internal\PlayerWalked;
use Illuminate\Database\Eloquent\Model;
use Ratchet\ConnectionInterface;

/**
 * DB
 * @property int $id
 * @property string $name
 * @property int $x
 * @property int $y
 * @property int $z
 * @property string $created_at
 * @property string $updated_at
 */
class Player extends Model
{
    const
        ROLE_PLAYER = 0,
        ROLE_GAMEMASTER = 1;


    protected $guarded = [];


    public ConnectionInterface $conn;

    public string $direction;

    public int $speed;


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

    public function walk(string $direction): void
    {
        $fromSQM = $this->getSQM();
        $targetPosition = ['x' => $this->x, 'y' => $this->y, 'z' => $this->z];

        if (in_array($direction, ['West', 'NorthWest', 'SouthWest'])) {
            $targetPosition['x']--;
        }
        if (in_array($direction, ['East', 'NorthEast', 'SouthEast'])) {
            $targetPosition['x']++;
        }
        if (in_array($direction, ['North', 'NorthEast', 'NorthWest'])) {
            $targetPosition['y']--;
        }
        if (in_array($direction, ['South', 'SouthEast', 'SouthWest'])) {
            $targetPosition['y']++;
        }

        $toSQM = World::getSQM(($targetPosition['x']), $targetPosition['y'], $targetPosition['z']);
        if ($toSQM && $toSQM->isWalkable()) {
            $this->x = $toSQM->x;
            $this->y = $toSQM->y;
            $this->z = $toSQM->z;
            $this->direction = $direction;
        }

        event(new PlayerWalked($this, $fromSQM, $toSQM));
    }

    public function teleport(SQM $toSQM): void
    {
        if (!$toSQM->hasGround()) {
            return;
        }

        $fromSQM = $this->getSQM();
        $this->x = $toSQM->x;
        $this->y = $toSQM->y;
        $this->z = $toSQM->z;

        event(new PlayerTeleported($this, $fromSQM, $toSQM));
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
            'name' => $this->name,
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            'direction' => $this->direction,
            'speed' => $this->speed
        ];
    }

    public function sendEvent(string $event, array $data = []): void
    {
        $this->conn->send(new WsEventRequest($event, $data));
    }

}
