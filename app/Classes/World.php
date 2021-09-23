<?php

namespace App\Classes;


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
