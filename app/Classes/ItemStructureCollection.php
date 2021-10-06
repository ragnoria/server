<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;

class ItemStructureCollection
{
    public static array $itemStructures = [];


    public static function init()
    {
        Log::info('Loading items..');

        try {
            foreach (DB::table('items')->get() as $item) {
                self::$itemStructures[$item->id] = (array) $item;
            }
        } catch (\Exception $e) {
            Log::error('Could not load items.');
            exit();
        }

    }

}
