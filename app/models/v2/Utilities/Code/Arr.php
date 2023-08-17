<?php

namespace v2\Utilities\Code;

use Illuminate\Support\Arr as IlluminateArr;



class Arr extends IlluminateArr
{



    /**
     * Filters a multi-dimentional array using the allowed keys specified in another 
     * multi-dimensional array
     * 
     * @param array $data array to be filetered
     * @param array $sieve array with specified allowed keys
     * @param string $all apply to all child element's
     * @return array
     */
    public static function deepKeySift(array $data, array $sieve, $all = '*'): array
    {
        $dot_data = self::dot($data);
        $dot_sieve = self::dot($sieve);

        $final_sieve = array_filter($dot_data, function ($item, $key) use ($dot_sieve, $all) {
            foreach ($dot_sieve as $p => $value) {
                $p = preg_replace(["/\.\*\./"], ["\..*\."], $p);
                if ($p[0] == '*') {
                    $p = "." . $p;
                }


                preg_match("/$p/", $key, $match);
                if (isset($match[0])) {
                    return true;
                }
            }

            return false;
        }, ARRAY_FILTER_USE_BOTH);

        return self::undot($final_sieve);
    }


    /**
     * Expands a dot notation array into a full multi-dimensional array.
     *
     * @param array $dotNotationArray
     *
     * @return array
     */
    public static function undot(array $dotNotationArray)
    {
        $array = [];
        foreach ($dotNotationArray as $key => $value) {
            self::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param array  $array
     * @param string $prepend
     *
     * @return array
     */
    public static function dot($array, $prepend = '')
    {
        $results = [];
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }
}
