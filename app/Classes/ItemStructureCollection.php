<?php

namespace App\Classes;


class ItemStructureCollection
{
    public static array $itemStructures = [];


    public static function init()
    {
        Log::info('Loading items..');

        try {
            foreach (scandir(resource_path('items/')) as $file) {
                if (in_array($file, ['.', '..'])) {
                    continue;
                }
                $item = json_decode(file_get_contents(resource_path('items/' . $file)), true);
                self::$itemStructures[$item['id']] = $item;
            }
        } catch (\Exception $e) {
            Log::error('Could not load items.');
            exit();
        }

    }

}
