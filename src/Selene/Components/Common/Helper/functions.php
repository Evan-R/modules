<?php

/**
 * This File is part of the Selene\Components\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

if (!function_exists('array_get')) {
    /**
     * array_get
     *
     * @param mixed $namespace
     * @param array $array
     * @param string $separator
     * @access
     * @return mixed
     */
    function array_get($namespace, array $array, $separator = '.')
    {
        $keys = explode($separator, $namespace);

        if (!isset($array[current($keys)])) {
            return;
        }

        while (count($keys) > 0) {
            $array = $array[array_shift($keys)];
        }
        return $array;
    }
}

if (!function_exists('array_set')) {
    /**
     * parse a segmented string to an array
     *
     * @param string $namespace
     * @param mixed  $value
     * @param array  $array
     * @param string $separator
     *
     * @return array
     */
    function array_set($namespace, $value, array &$array = [], $separator = '.')
    {
        $keys = explode($separator, $namespace);
        $key = array_shift($keys);

        if (!count($keys) && $array[$key] = $value) {
            return $array;
        }

        if (!isset($array[$key])) {
            $array[$key] = [];
        }

        return array_set(implode($separator, $keys), $value, $array[$key], $separator);
    }
}

if (!function_exists('array_pluck')) {
    /**
     * array_pluck
     *
     * @param mixed $key
     * @param mixed $array
     * @access
     * @return mixed
     */
    function array_pluck($key, array $array)
    {
        return array_map(function ($item) use ($key)
        {
            return is_object($item) ? $item->$key : $item[$key];
        }, $array);
    }
}

if (!function_exists('array_zip')) {
    /**
     * array_zip
     *
     * @access
     * @return mixed
     */
    function array_zip()
    {
        $args = func_get_args();
        $count = count($args);

        $out = [];

        for ($i = 0; $i < $count; $i++) {
            $out[$i] = array_pluck($i, $args);
        }
        return $out;
    }
}

if (!function_exists('array_max')) {
    /**
     * array_max
     *
     * @param array $args
     * @access
     * @return mixed
     */
    function array_max(array $args)
    {
        uasort($args, function ($a, $b) {
            return count($a) < count($b) ? 1 : -1;
        });
        return count(head($args));
    }
}

if (!function_exists('array_min')) {
    /**
     * array_min
     *
     * @param array $args
     * @access
     * @return mixed
     */
    function array_min(array $args)
    {
        usort($args, function ($a, $b) {
            return count($a) < count($b) ? 1 : -1;
        });
        return count(tail($args));
    }
}

if (!function_exists('head')) {
    /**
     * array_head
     *
     * @param array $array
     * @access
     * @return mixed
     */
    function head(array $array)
    {
        return reset($array);
    }
}

if (!function_exists('tail')) {
    /**
     * array_tail
     *
     * @access
     * @return mixed
     */
    function tail(array $array)
    {
        return end($array);
    }
}

if (!function_exists('array_numeric')) {
    /**
     * array_numeric
     *
     * @param array $array
     *
     * @return boolean
     */
    function array_numeric(array $array)
    {
        return ctype_digit(implode('', array_keys($array)));
    }
}

if (!function_exists('array_compact')) {
    /**
     * array_compact
     *
     * @param array $array
     *
     * @return array
     */
    function array_compact(array $array)
    {
        $out = array_filter($array, function ($item)
        {
            return false !== (bool)$item;
        });
        return array_numeric($out) ? array_values($out) : $out;
    }
}

if (!function_exists('clear_value')) {
    /**
     * null a given value in case it's an empty string
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function clear_value($value)
    {
        return ((is_string($value) && 0 === strlen(trim($value))) || is_null($value)) ? null : $value;
    }
}

if (!function_exists('equals')) {
    /**
     * equals
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return boolean
     */
    function equals($value, $comparator)
    {
        return $value == $comparator;
    }
}

if (!function_exists('same')) {
    /**
     * same
     *
     * @param mixed $a
     * @param mixed $b
     * @access
     * @return mixed
     */
    function same($value, $comparator)
    {
        return  $value === $comparator;
    }
}

if (!function_exists('str_camel_case')) {
    /**
     * camelcase notataion
     *
     * convert lowdash to camelcase notation
     *
     * @param mixed $str
     * @access
     * @return mixed
     */
    function str_camel_case($str)
    {
        return lcfirst(str_camel_case_all($str));
    }
}

if (!function_exists('str_camel_case_all')) {
    /**
     * all camelcase notataion
     *
     * @param string $string
     *
     * @return string
     */
    function str_camel_case_all($string)
    {
        return str_replace(' ', null, ucwords(str_replace(['-', '_'], ' ', $string)));
    }
}

if (!function_exists('str_low_dash')) {
    /**
     * convert camelcase to low dash notation
     *
     * @param string $string
     *
     * @return string
     */
    function str_low_dash($string)
    {
        return strtolower(preg_replace('/[A-Z]/', '_$0', $string));
    }
}

if (!function_exists('str_starts_with')) {
    /**
     * determine if a string starts wiht a given sequence
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    function str_starts_with($sequence, $string)
    {
        return 0 === strpos($string, $sequence);
    }
}

if (!function_exists('stri_starts_with')) {
    /**
     * determine if a string starts wiht a given sequence
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    function stri_starts_with($sequence, $string)
    {
        return 0 === stripos($string, $sequence);
    }
}

if (!function_exists('contained_and_starts_with')) {
    /**
     * contained_and_starts_with
     *
     * @param string $sequence
     * @param array $comparable
     * @access
     * @return boolean
     */
    function contained_and_starts_with(array $comparable, $string)
    {
        while (count($comparable)) {
            if (true === str_starts_with(array_shift($comparable), $string)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('contained_and_ends_with')) {
    /**
     * contained_and_ends_with
     *
     * @param array $comparable
     * @param mixed $string
     * @access
     * @return mixed
     */
    function contained_and_ends_with(array $comparable, $string)
    {
        while (count($comparable)) {
            if (true === str_ends_with(array_shift($comparable), $string)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('str_ends_with')) {
    /**
     * determine if a string ends wiht a given sequence
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    function str_ends_with($sequence, $string)
    {
        return (strlen($string) - strlen($sequence)) === strpos($string, $sequence);
    }
}

if (!function_exists('stri_ends_with')) {
    /**
     * determine if a string ends wiht a given sequence
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    function stri_ends_with($sequence, $string)
    {
        return (strlen($string) - strlen($sequence)) === stripos($string, $sequence);
    }
}

if (!function_exists('str_contains')) {
    /**
     * determine if a string contains a gicen string sequence
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    function str_contains($sequence, $string)
    {
        return false !== strpos($string, $sequence);
    }
}

if (!function_exists('stri_contains')) {
    /**
     * str_contains
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    function stri_contains($sequence, $string)
    {
        return false !== stripos($string, $sequence);
    }
}

if (!function_exists('substr_after')) {
    /**
     * returns the substring after the first occurance of a given character
     *
     * @param string $char
     * @param string $string
     *
     * @return string|boolean
     */
    function substr_after($char, $string)
    {
        return false !== ($pos = strpos($string, $char)) ? substr($string, $pos + 1) : false;
    }
}

if (!function_exists('substri_after')) {
    /**
     * returns the substring after the first occurance of a given character
     * (case insensitive)
     *
     * @param string $char
     * @param string $string
     *
     * @return string|boolean
     */
    function substri_after($char, $string)
    {
        return false !== ($pos = stripos($string, $char)) ? substr($string, $pos + 1) : false;
    }
}

if (!function_exists('substr_before')) {
    /**
     * returns the substring before the first occurance of a given character
     *
     * @param string $char
     * @param string $string
     *
     * @return string|boolean
     */
    function substr_before($char, $string)
    {
        return false !== ($pos = strpos($string, $char)) ? substr($string, 0, $pos) : false;
    }
}

if (!function_exists('substri_before')) {
    /**
     * returns the substring before the first occurance of a given character
     * (case insensitive)
     *
     * @param string $char
     * @param string $string
     *
     * @return string|boolean
     */
    function substri_before($char, $string)
    {
        return false !== ($pos = stripos($string, $char)) ? substr($string, 0, $pos) : false;
    }
}

if (!function_exists('str_concat')) {
    /**
     * concatenate string|number|object segments
     *
     * Note that passing an object that doesn't specify a `__toString` method
     * this will raise a runtime exception
     *
     * @param string $char
     * @param string $string
     *
     * @return string
     */
    function str_concat()
    {
        return vsprintf(str_repeat('%s', count($args = func_get_args())),  $args);
    }

}

if (!function_exists('get_require')) {

    /**
     * get_require
     *
     * @param mixed $file
     * @access 
     * @return mixed
     */
    function get_require($file)
    {
        return require($file);
    }
}
