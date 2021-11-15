<?php

namespace App\Console\Commands;

use App\Classes\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateItems extends Command
{
    protected $signature = 'generate:items';

    protected $description = 'Generates items.json';


    public function handle()
    {
        Log::info('Generating items.json...');

        $items = [];
        foreach (DB::table('items')->get() as $item) {
            $sprites = [];
            foreach(json_decode($item->sprites) as $sprite) {
                if (!$img = file_get_contents(resource_path('items/' .$sprite. '.png'))) {
                    Log::error('Could not load sprite: ' .$sprite);
                    return;
                }
                $sprites[] = base64_encode($img);
            }
            $item->sprites = $sprites;
            $items[] = $item;
        }

        $fp = fopen(resource_path('items.json'), 'w+');
        fwrite($fp, json_encode($items, JSON_PRETTY_PRINT));
        fclose($fp);

        Log::success('items.json generated successfully!');
    }

}
