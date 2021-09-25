<?php

namespace App\Classes;

class MiscHelper
{
    static function getFieldsBetween(array $A, array $B): array
    {
        $results = [];

        $A['X'] += 0.5;
        $A['Y'] += 0.5;
        $B['X'] += 0.5;
        $B['Y'] += 0.5;

        if ($A['X'] - $B['X'] === (float)0) {
            if ($B['Y'] > $A['Y']) {
                for ($i = 1; $i < ($B['Y'] - $A['Y']); $i++) {
                    $x = (int)floor($A['X']);
                    $y = (int)floor($A['Y'] + $i);
                    $results[$y][$x] = true;
                }
            } else {
                for ($i = 1; $i < ($A['Y'] - $B['Y']); $i++) {
                    $x = (int)floor($B['X']);
                    $y = (int)floor($B['Y'] + $i);
                    $results[$y][$x] = true;
                }
            }
            return $results;
        }

        $a = (($A['Y'] - $B['Y']) * (-1)) / (($A['X'] - $B['X']) * (-1));
        $b = $A['Y'] - ($A['X'] * $a);
        if ($B['X'] > $A['X']) {
            for ($i = 1; $i < ($B['X'] - $A['X']); $i++) {
                $y = (int)floor($a * ($A['X'] + $i) + $b);
                $x = (int)floor($A['X'] + $i);
                $results[$y][$x] = true;
            }
        } else {
            for ($i = 1; $i < ($A['X'] - $B['X']); $i++) {
                $y = (int)floor($a * ($B['X'] + $i) + $b);
                $x = (int)floor($B['X'] + $i);
                $results[$y][$x] = true;
            }
        }

        if ($B['Y'] > $A['Y']) {
            for ($i = 1; $i < ($B['Y'] - $A['Y']); $i++) {
                $x = (int)floor((($A['Y'] + $i) - $b) / $a);
                $y = (int)floor($A['Y'] + $i);
                $results[$y][$x] = true;
            }
        } else {
            for ($i = 1; $i < ($A['Y'] - $B['Y']); $i++) {
                $x = (int)floor((($B['Y'] + $i) - $b) / $a);
                $y = (int)floor($B['Y'] + $i);
                $results[$y][$x] = true;
            }
        }

        return $results;
    }

}
