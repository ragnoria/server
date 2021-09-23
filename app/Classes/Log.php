<?php

namespace App\Classes;

class Log
{
    const
        TEXT_BLACK = '0;30',
        TEXT_DARK_GREY = '1;30',
        TEXT_RED = '0;31',
        TEXT_LIGHT_RED = '1;31',
        TEXT_GREEN = '0;32',
        TEXT_LIGHT_GREEN = '1;32',
        TEXT_BROWN = '0;33',
        TEXT_YELLOW = '1;33',
        TEXT_BLUE = '0;34',
        TEXT_LIGHT_BLUE = '1;34',
        TEXT_MAGENTA = '0;35',
        TEXT_LIGHT_MAGENTA = '1;35',
        TEXT_CYAN = '0;36',
        TEXT_LIGHT_CYAN = '1;36',
        TEXT_LIGHT_GREY = '0;37',
        TEXT_WHITE = '1;37';

    const
        BACKGROUND_BLACK = '40',
        BACKGROUND_RED = '41',
        BACKGROUND_GREEN = '42',
        BACKGROUND_YELLOW = '43',
        BACKGROUND_BLUE = '44',
        BACKGROUND_MAGENTA = '45',
        BACKGROUND_CYAN = '46',
        BACKGROUND_LIGHT_GREY = '47';


    public static function log(string $msg, string $fg = self::TEXT_LIGHT_GREY, string $bg = self::BACKGROUND_BLACK): void
    {
        $date = date('Y-m-d H:i:s');
        echo "\e[{$fg};{$bg}m[{$date}] {$msg}\e[0m\r\n";
    }

    public static function info(string $msg): void
    {
        static::log($msg, self::TEXT_LIGHT_GREY, self::BACKGROUND_BLACK);
    }

    public static function warning(string $msg): void
    {
        static::log($msg, self::TEXT_BROWN, self::BACKGROUND_BLACK);
    }

    public static function error(string $msg): void
    {
        static::log($msg, self::TEXT_WHITE, self::BACKGROUND_RED);
    }

    public static function success(string $msg): void
    {
        static::log($msg, self::TEXT_GREEN, self::BACKGROUND_BLACK);
    }

}
