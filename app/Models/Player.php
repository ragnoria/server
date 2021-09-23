<?php

namespace App\Models;

use App\Classes\Client\Effects;
use App\Classes\Client\Events;
use App\Classes\SQM;
use App\Classes\World;
use App\Classes\WsEventRequest;
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
    protected $guarded = [];


    public ConnectionInterface $conn;

    public string $direction;

    public int $speed;


    public function move(string $direction): bool
    {
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

        /** @var SQM $SQM */
        $SQM = World::getSQM(($targetPosition['x']), $targetPosition['y'], $targetPosition['z']);
        if (!$SQM->isWalkable()) {
            return false;
        }

        $this->x = $targetPosition['x'];
        $this->y = $targetPosition['y'];
        $this->z = $targetPosition['z'];
        $this->direction = $direction;

        return true;
    }

    public function login()
    {
        World::$players->attach($this);
        foreach (World::$players as $player) {
            $player->sendEvent(Events::EFFECT_RUN, [
                'effect' => Effects::LOGIN,
                'x' => $this->x,
                'y' => $this->y,
                'z' => $this->z
            ]);
        }
    }

    public function logout()
    {
        foreach (World::$players as $player) {
            $player->sendEvent(Events::EFFECT_RUN, [
                'effect' => Effects::POOF,
                'x' => $this->x,
                'y' => $this->y,
                'z' => $this->z
            ]);
        }
        World::$players->detach($this);
        $this->save();
    }

    public function getArea(): array
    {
        $factor_x = (ceil(env('GAME_CLIENT_SQM_WIDTH') / 2) - 1);
        $factor_y = (ceil(env('GAME_CLIENT_SQM_HEIGHT') / 2) - 1);
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

    public function sendEvent(string $event, array $data = []): void
    {
        $this->conn->send(new WsEventRequest($event, $data));
    }

}
