<?php

namespace App\Models;

use App\Classes\Client\Effects;
use App\Classes\Client\Events;
use App\Classes\Helper;
use App\Classes\Item;
use App\Classes\Log;
use App\Classes\Outfit;
use App\Classes\SQM;
use App\Classes\World;
use App\Classes\WsEventRequest;
use App\Events\Internal\PlayerLoggedIn;
use App\Events\Internal\PlayerLoggedOut;
use App\Events\Internal\PlayerTeleported;
use App\Events\Internal\PlayerWalked;
use App\Events\Internal\WalkedIn;
use App\Events\Internal\WalkedOut;
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
        World::$players->attach($this);
        event(new PlayerLoggedIn($this));
        event(new WalkedIn($this, $this->getSQM()));

        Log::info("Player '{$this->name}' logged in. Players online: " . World::$players->count() . ".");
    }

    public function logout(): void
    {
        World::$players->detach($this);
        event(new PlayerLoggedOut($this));
        event(new WalkedOut($this, $this->getSQM()));
        $this->conn->close();
        $this->save();

        Log::info("Player '{$this->name}' logged out. Players online: " . World::$players->count() . ".");
    }

    public function walk(string $direction): void
    {
        $fromSQM = $this->getSQM();
        $toSQM = Helper::getSQMAfterMove($this->getSQM(), $direction);

        if ($toSQM && $toSQM->isWalkable()) {
            $this->x = $toSQM->x;
            $this->y = $toSQM->y;
            $this->z = $toSQM->z;
            $this->direction = $direction;

            event(new PlayerWalked($this, $fromSQM, $toSQM));
            event(new WalkedOut($this, $fromSQM));
            event(new WalkedIn($this, $toSQM));
        }
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
        event(new WalkedOut($this, $fromSQM));
        event(new WalkedIn($this, $toSQM));
    }

    public function heal(int $power): void
    {
        if ($this->hp_max <= $this->hp + $power) {
            $this->hp = $this->hp_max;
        } else {
            $this->hp += $power;
        }
    }

    public function hurt(int $power, string $effect = Effects::FIRE): void
    {
        if ($this->hasPermissions(Player::ROLE_GAMEMASTER)) {
            $power = 0;
        }

        if ($power > 0) {
            if ($this->hp <= $power) {
                $this->hp = 0;
            } else {
                $this->hp -= $power;
            }

            foreach (World::getNearbyPlayers($this->getSQM()) as $player) {
                $player->sendEvent(Events::PLAYER_HURT, [
                    'player_id' => $this->id,
                    'power' => $power,
                    'effect' => $effect,
                    'hp' => $this->hp,
                    'hp_max' => $this->hp_max,
                ]);
            }
        } else {
            foreach(World::getNearbyPlayers($this->getSQM()) as $player) {
                $player->sendEvent(Events::RUN_EFFECT, [
                    'id' => Effects::POOF,
                    'x' => $this->x,
                    'y' => $this->y,
                    'z' => $this->z
                ]);
            }
        }

        if ($this->hp === 0) {
            $this->die();
        }
    }

    public function die(): void
    {
        $this->getSQM()->addItem(new Item(11, 1));
        foreach (World::getNearbyPlayers($this->getSQM()) as $player) {
            $player->sendEvent(Events::UPDATE_SQM, [
                'x' => $this->x,
                'y' => $this->y,
                'z' => $this->z,
                'stack' => $this->getSQM()->stack,
            ]);
        }

        $this->logout();

        $this->hp = $this->hp_max;
        $this->x = config('ragnoria.respawn.x');
        $this->y = config('ragnoria.respawn.y');
        $this->z = config('ragnoria.respawn.z');
        $this->save();
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
