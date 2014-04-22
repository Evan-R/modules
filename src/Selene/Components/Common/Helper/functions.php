<?php

/**
 * This File is part of the Selene\Components\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

if (!function_exists('arrayGet')) {
    /**
     * array_get
     *
     * @param mixed $namespace
     * @param array $array
     * @param string $separator
     * @access
     * @return mixed
     */
    function arrayGet(array $array, $namespace = null, $separator = '.')
    {
        if (!is_string($namespace)) {
            return $array;
        }

        $keys = explode($separator, $namespace);

        while (count($keys) > 0 and !is_null($array)) {
            $key = array_shift($keys);
            $array = isset($array[$key]) ? $array[$key] : null;
        }
        return $array;
    }
}

if (!function_exists('arraySet')) {
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
    function arraySet($namespace, $value, array &$input = [], $separator = '.')
    {
        $keys  = explode($separator, $namespace);
        $pointer = &$input;

        while (count($keys) > 0) {
            $key = array_shift($keys);
            $pointer[$key] = isset($pointer[$key]) ? $pointer[$key] : [];
            $pointer = &$pointer[$key];
        }

        $pointer = $value;
        return $input;
    }
}

if (!function_exists('array_column')) {

    /**
     * array_column
     *
     * @see http://www.php.net/manual/en/function.array-walk.php
     * @return array
     */
    function array_column($array, $key, $index = null)
    {
        $out = [];
        array_walk(
            $array,
            function ($item) use ($key, $index, &$out) {
                $index ? $out[$item[$index]] = $item[$key] : $out[] = $item[$key];
            }
        );
        return $out;
    };
}

if (!function_exists('arrayPluck')) {
    /**
     * array_pluck
     *
     * @param mixed $key
     * @param mixed $array
     *
     * @return array
     */
    function arrayPluck($key, array $array)
    {
        return array_map(
            function ($item) use ($key) {
                return is_object($item) ? $item->$key : $item[$key];
            },
            $array
        );
    }
}

if (!function_exists('arrayZip')) {
    /**
     * array_zip
     *
     * @access
     * @return mixed
     */
    function arrayZip()
    {
        $args = func_get_args();
        $count = count($args);

        $out = [];

        for ($i = 0; $i < $count; $i++) {
            $out[$i] = arrayPluck($i, $args);
        }
        return $out;
    }
}

if (!function_exists('arrayMax')) {
    /**
     * array_max
     *
     * @param array $args
     * @access
     * @return mixed
     */
    function arrayMax(array $args)
    {
        uasort(
            $args,
            function ($a, $b) {
                return count($a) < count($b) ? 1 : -1;
            }
        );
        return count(head($args));
    }
}

if (!function_exists('arrayMin')) {
    /**
     * array_min
     *
     * @param array $args
     * @access
     * @return mixed
     */
    function arrayMin(array $args)
    {
        usort(
            $args,
            function ($a, $b) {
                return count($a) < count($b) ? 1 : -1;
            }
        );
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

if (!function_exists('arrayNumeric')) {
    /**
     * array_numeric
     *
     * @param array $array
     *
     * @return boolean
     */
    function arrayNumeric(array $array)
    {
        return ctype_digit(implode('', array_keys($array)));
    }
}

if (!function_exists('arrayCompact')) {
    /**
     * array_compact
     *
     * @param array $array
     *
     * @return array
     */
    function arrayCompact(array $array)
    {
        $out = array_filter(
            $array,
            function ($item) {
                return false !== (bool)$item;
            }
        );
        return arrayNumeric($out) ? array_values($out) : $out;
    }
}

if (!function_exists('clearValue')) {
    /**
     * null a given value in case it's an empty string
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function clearValue($value)
    {
        return is_string($value) && 0 === strlen(trim($value)) ? null : $value;
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

if (!function_exists('strCamelCase')) {
    /**
     * camelcase notataion
     *
     * convert lowdash to camelcase notation
     *
     * @param mixed $str
     * @access
     * @return mixed
     */
    function strCamelCase($str, $replace = ['-' => ' ', '_' => ' '])
    {
        return lcfirst(strCamelCaseAll($str, $replace));
    }
}

if (!function_exists('strCamelCaseAll')) {
    /**
     * all camelcase notataion
     *
     * @param string $string
     *
     * @return string
     */
    function strCamelCaseAll($string, array $replace = ['-' => ' ', '_' => ' '])
    {
        return strtr(ucwords(strtr($string, $replace)), [' ' => '']);
    }
}

if (!function_exists('strLowDash')) {
    /**
     * convert camelcase to low dash notation
     *
     * @param string $string
     *
     * @return string
     */
    function strLowDash($string)
    {
        return strtolower(preg_replace('#[A-Z]#', '_$0', lcfirst($string)));
    }
}

if (!function_exists('strStartsWith')) {
    /**
     * determine if a string starts wiht a given sequence
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    function strStartsWith($sequence, $string)
    {
        return 0 === strpos($string, $sequence);
    }
}

if (!function_exists('striStartsWith')) {
    /**
     * determine if a string starts wiht a given sequence
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    function striStartsWith($sequence, $string)
    {
        return 0 === stripos($string, $sequence);
    }
}

if (!function_exists('containedAndStartsWith')) {
    /**
     * contained_and_starts_with
     *
     * @param string $sequence
     * @param array $comparable
     * @access
     * @return boolean
     */
    function containedAndStartsWith(array $comparable, $string)
    {
        while (count($comparable)) {
            if (true === strStartsWith(array_shift($comparable), $string)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('containedAndEndsWith')) {
    /**
     * contained_and_ends_with
     *
     * @param array $comparable
     * @param mixed $string
     * @access
     * @return mixed
     */
    function containedAndEndsWith(array $comparable, $string)
    {
        while (count($comparable)) {
            if (true === strEndsWith(array_shift($comparable), $string)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('strEndsWith')) {
    /**
     * determine if a string ends wiht a given sequence
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    function strEndsWith($sequence, $string)
    {
        return (strlen($string) - strlen($sequence)) === strpos($string, $sequence);
    }
}

if (!function_exists('striEndsWith')) {
    /**
     * determine if a string ends wiht a given sequence
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    function striEndsWith($sequence, $string)
    {
        return (strlen($string) - strlen($sequence)) === stripos($string, $sequence);
    }
}

if (!function_exists('strContains')) {
    /**
     * determine if a string contains a gicen string sequence
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    function strContains($sequence, $string)
    {
        return false !== strpos($string, $sequence);
    }
}

if (!function_exists('striContains')) {
    /**
     * str_contains
     *
     * @param string $sequence
     * @param string $string
     *
     * @return boolean
     */
    function striContains($sequence, $string)
    {
        return false !== stripos($string, $sequence);
    }
}

if (!function_exists('substrAfter')) {
    /**
     * returns the substring after the first occurance of a given character
     *
     * @param string $char
     * @param string $string
     *
     * @return string|boolean
     */
    function substrAfter($char, $string)
    {
        return false !== ($pos = strpos($string, $char)) ? substr($string, $pos + 1) : false;
    }
}

if (!function_exists('substriAfter')) {
    /**
     * returns the substring after the first occurance of a given character
     * (case insensitive)
     *
     * @param string $char
     * @param string $string
     *
     * @return string|boolean
     */
    function substriAfter($char, $string)
    {
        return false !== ($pos = stripos($string, $char)) ? substr($string, $pos + 1) : false;
    }
}

if (!function_exists('substrBefore')) {
    /**
     * returns the substring before the first occurance of a given character
     *
     * @param string $char
     * @param string $string
     *
     * @return string|boolean
     */
    function substrBefore($char, $string)
    {
        return false !== ($pos = strpos($string, $char)) ? substr($string, 0, $pos) : false;
    }
}

if (!function_exists('substriBefore')) {
    /**
     * returns the substring before the first occurance of a given character
     * (case insensitive)
     *
     * @param string $char
     * @param string $string
     *
     * @return string|boolean
     */
    function substriBefore($char, $string)
    {
        return false !== ($pos = stripos($string, $char)) ? substr($string, 0, $pos) : false;
    }
}

if (!function_exists('strConcat')) {
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
    function strConcat()
    {
        return vsprintf(str_repeat('%s', count($args = func_get_args())), $args);
    }

}


if (!function_exists('strEscapeStr')) {
    function strEscapeStr($str, $needle)
    {
        return str_replace($needle, $needle.$needle, $str);
    }
}

if (!function_exists('strUnescapeStr')) {

    function strUnescapeStr($str, $needle)
    {
        return str_replace($needle.$needle, $needle, $str);
    }
}
if (!function_exists('strWrapStr')) {

    function strWrapStr($str, $begin, $end = null)
    {
        return sprintf('%s%s%s', $begin, $str, $end ?: $begin);
    }
}

if (!function_exists('strRand')) {
    /**
     * Generates a random string with the given length.
     *
     * @param integer $length
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeExceptionn
     *
     * @access
     * @return string
     */
    function strRand($length)
    {
        if (!is_int($length)) {
            throw new \InvalidArgumentException(
                sprintf('strRand expects first argument to be integer, instead saw %s.'. gettype($length))
            );
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            if (null === ($bytes = openssl_random_pseudo_bytes($length * 2))) {
                throw new \RuntimeException('Cannot generate random string');
            }
            return substr(str_replace(['/', '=', '+'], '', base64_encode($bytes)), 0, $length);
        } else {
            return strQuickRand($length);
        }
    }
}

if (!function_exists('strQuickRand')) {
    function strQuickRand($length)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($chars, 5)), 0, $length);
    }
}

if (!function_exists('getRequire')) {

    /**
     * get_require
     *
     * @param mixed $file
     * @access
     * @return mixed
     */
    function getRequire($file)
    {
        return require($file);
    }
}

if (!function_exists('fluent')) {
    function fluent($object)
    {
        return $object;
    }
}
