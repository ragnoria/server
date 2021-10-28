<?php

namespace App\Classes;

class ItemStructureCollection
{
    public static array $itemStructures = [];


    public static function init()
    {
        Log::info('Loading items..');

        try {
            if (!file_exists(resource_path('items.json'))) {
                throw new \Exception('File `items.json` does not exists. Use `artisan generate:items` command to create.');
            }
            if (!$itemsJson = file_get_contents(resource_path('items.json'))) {
                throw new \Exception('File `items.json` cannot be loaded or is empty.');
            }
            if (!$items = json_decode($itemsJson, true)) {
                throw new \Exception('File `items.json` contains invalid json string.');
            }
            foreach ($items as $item) {
                self::$itemStructures[$item['id']] = $item;
            }
        } catch (\Exception $e) {
            Log::error('Could not load items - ' . $e->getMessage());
            exit();
        }

    }

}
