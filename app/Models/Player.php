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
        foreach (World::getNearbyPlayers($this->getSQM()) as $player) {
            $player->sendEvent(Events::RUN_EFFECT, [
                'effect' => Effects::LOGIN,
                'x' => $this->x,
                'y' => $this->y,
                'z' => $this->z
            ]);
            if ($player !== $this) {
                $player->sendEvent(Events::MOVE_PLAYER, [
                    'player' => $this,
                    'direction' => null
                ]);
            }
        }
    }

    public function logout(): void
    {
        foreach (World::getNearbyPlayers($this->getSQM()) as $player) {
            $player->sendEvent(Events::RUN_EFFECT, [
                'effect' => Effects::POOF,
                'x' => $this->x,
                'y' => $this->y,
                'z' => $this->z
            ]);
            $player->sendEvent(Events::REMOVE_PLAYER, [
                'playerId' => $this->id,
            ]);
        }
        World::$players->detach($this);
        $this->save();
    }

    public function move(string $direction): void
    {
        $fromSQM = $this->getSQM();
        $playersOnAreaBeforeStep = World::getNearbyPlayers($fromSQM);
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
        if ($SQM->isWalkable()) {

            $this->x = $targetPosition['x'];
            $this->y = $targetPosition['y'];
            $this->z = $targetPosition['z'];
            $this->direction = $direction;

            $playersStillOnArea = [];
            foreach (World::getNearbyPlayers($this->getSQM()) as $player) if ($player !== $this) {
                $player->sendEvent(Events::MOVE_PLAYER, [
                    'player' => $this->toArray(),
                    'direction' => $fromSQM->z == $this->z ? $direction : null
                ]);
                if (!in_array($player, $playersOnAreaBeforeStep)) {
                    $this->sendEvent(Events::MOVE_PLAYER, [
                        'player' => $player->toArray(),
                        'direction' => null
                    ]);
                }
                $playersStillOnArea[] = $player;
            }

            foreach ($playersOnAreaBeforeStep as $player) if ($player !== $this) {
                if (!in_array($player, $playersStillOnArea)) {
                    $player->sendEvent(Events::REMOVE_PLAYER, [
                        'playerId' => $this->id
                    ]);
                    $this->sendEvent(Events::REMOVE_PLAYER, [
                        'playerId' => $player->id
                    ]);
                }
            }
        }

        $moved = $fromSQM !== $this->getSQM();

        $this->sendEvent(Events::CONFIRM_STEP, [
            'status' => $moved ? 'success' : 'failed',
            'area' => $moved ? $this->getArea() : null,
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            'direction' => $this->direction,
        ]);
    }

    public function teleport(SQM $sqm): void
    {
        $fromSQM = $this->getSQM();
        $playersOnAreaBeforeStep = World::getNearbyPlayers($fromSQM);

        $this->x = $sqm->x;
        $this->y = $sqm->y;
        $this->z = $sqm->z;

        $playersStillOnArea = [];
        foreach (World::getNearbyPlayers($this->getSQM()) as $player) {
            if ($player !== $this) {
                $player->sendEvent(Events::MOVE_PLAYER, [
                    'player' => $this->toArray(),
                    'direction' => null
                ]);
                if (!in_array($player, $playersOnAreaBeforeStep)) {
                    $this->sendEvent(Events::MOVE_PLAYER, [
                        'player' => $player->toArray(),
                        'direction' => null
                    ]);
                }
                $playersStillOnArea[] = $player;
            }
            $player->sendEvent(Events::RUN_EFFECT, [
                'effect' => Effects::LOGIN,
                'x' => $this->x,
                'y' => $this->y,
                'z' => $this->z
            ]);
        }
        foreach ($playersOnAreaBeforeStep as $player) if ($player !== $this) {
            if (!in_array($player, $playersStillOnArea)) {
                $player->sendEvent(Events::REMOVE_PLAYER, [
                    'playerId' => $this->id
                ]);
                $this->sendEvent(Events::REMOVE_PLAYER, [
                    'playerId' => $player->id
                ]);
            }
        }
        $this->sendEvent(Events::UPDATE_POSITION, [
            'status' => 'success',
            'area' => $this->getArea(),
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            'direction' => $this->direction,
        ]);
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
