<?php

namespace App\Classes;

use App\Models\Player;

class World
{
    private static array $grid;

    public static \SplObjectStorage $players;


    public static function init()
    {
        self::$players = new \SplObjectStorage();
        self::generateTerrain();
    }

    public static function getSQM(int $x, int $y, int $z): ?SQM
    {
        return self::$grid[$z][$y][$x] ?? null;
    }

    /**
     * @return SQM[] (does not include from & to SQMs)
     */
    public static function getSQMsBetween(SQM $fromSQM, SQM $toSQM): array
    {
        $result = [];
        $fields = Helper::getFieldsBetween(
            ['X' => $fromSQM->x, 'Y' => $fromSQM->y],
            ['X' => $toSQM->x, 'Y' => $toSQM->y]
        );
        foreach ($fields as $y => $row) {
            foreach ($row as $x => $tile) {
                if (self::getSQM($x, $y, $fromSQM->z)) {
                    $result[] = self::getSQM($x, $y, $fromSQM->z);
                }
            }
        }

        return $result;
    }

    /**
     * @return Player[]
     */
    public static function getNearbyPlayers(SQM $sqm): array
    {
        $players = [];

        $factor_x = ceil(config('ragnoria.area.width') / 2) - 1;
        $factor_y = ceil(config('ragnoria.area.height') / 2) - 1;
        $range_x = range(($sqm->x - $factor_x), ($sqm->x + $factor_x));
        $range_y = range(($sqm->y - $factor_y), ($sqm->y + $factor_y));

        foreach (self::$players as $player) {
            if (in_array($player->x, $range_x) && in_array($player->y, $range_y)) {
                $players[] = $player;
            }
        }

        return $players;
    }


    private static function generateTerrain()
    {
        Log::info('Loading terrain..');

        try {
            $terrain = json_decode(
                file_get_contents(
                    resource_path('map/terrain.json')
                )
            );
        } catch (\Exception $e) {
            Log::error('Could not load terrain.');
            exit();
        }

        foreach ($terrain as $z => $floor) {
            foreach ($floor as $y => $row) {
                foreach ($row as $x => $stack) {
                    $sqm = new SQM($x, $y, $z);
                    foreach ($stack as $itemId) {
                        $sqm->addItem(new Item($itemId, 0, null));
                    }
                    self::$grid[$z][$y][$x] = $sqm;
                }
            }
        }
    }

}
