<?php
namespace App\Helpers;

class String {
    public static function named($format, $args) {
        $names = preg_match_all('/%\((.*?)\)/', $format, $matches, PREG_SET_ORDER);

        $values = array();
        foreach($matches as $match) {
            $values[] = $args[$match[1]];
        }

        $format = preg_replace('/%\((.*?)\)/', '%', $format);
        return vsprintf($format, $values);
    }
}