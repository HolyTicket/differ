<?php
namespace App\Helpers;

/**
 * Class String
 * String Helper function
 * @package App\Helpers
 */
class String {
    /**
     * Sprintf function that accepts an named array. Very handy for creating some SQL staetments
     * @param $format
     * @param $args
     * @return string
     */
    public static function named($format, $args) {
        $names = preg_match_all('/%\((.*?)\)/', $format, $matches, PREG_SET_ORDER);

        $values = array();
        foreach($matches as $match) {
            $values[] = $args[$match[1]];
        }

        $format = preg_replace('/%\((.*?)\)/', '%', $format);
        return vsprintf($format, $values);
    }

    /**
     * Sprintf function. Not much benefit above sprintf() here yet
     * @return int
     */
    public static function sprintf() {
        $argv = func_get_args();
        $format = array_shift( $argv );
        return vprintf($format, $argv);
    }

}