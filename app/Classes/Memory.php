<?php

namespace App\Classes;

class Memory
{

    public static function info(): string
    {
        return self::allocated() . ' (real: ' . self::allocated(true) . ') / ' . self::limit();
    }

    public static function allocated($real = false): string
    {
        return self::stringify(memory_get_usage($real));
    }

    public static function limit(): string
    {
        return ini_get('memory_limit');
    }

    private static function stringify(int $size): string
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

}
