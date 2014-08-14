<?php

/**
 * This File is part of the Selene\Module\Common\Helper package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Helper;

use \RecursiveArrayIterator;
use \RecursiveIteratorIterator;

/**
 * @class ListHelper ListHelper
 *
 * @package \Users\malcolm\www\selene_source\src\Selene\Module\Common\Helper
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
final class ListHelper
{
    /**
     * isTraversable
     *
     * @param mixed $data
     *
     * @access public
     * @return boolean
     */
    public static function isTraversable($data)
    {
        return is_array($data) || $data instanceof \Traversable;
    }

    /**
     * Flattens a multi dimensional array.
     *
     * @param array $array
     *
     * @access public
     * @return array
     */
    public static function arrayFlatten(array $array)
    {
        $out = [];

        foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $key => $item) {

            if (is_int($key)) {
                $out[] = $item;
                continue;
            }

            $out[$key] = $item;
        }

        return $out;
    }

    /**
     * arrayGet
     *
     * @param array $array
     * @param mixed $namespace
     * @param string $separator
     *
     * @access public
     * @return mixed
     */
    public static function arrayGet(array $array, $namespace = null, $separator = '.')
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
    public static function arraySet(array &$input, $namespace, $value, $separator = '.')
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

    public static function arrayUnset(array &$array, $namespace, $separator = '.')
    {
        if (!is_string($namespace)) {
            return $array;
        }

        $keys = explode($separator, $namespace);

        while (($count = count($keys)) > 0 and !is_null($array)) {
            $key = array_shift($keys);
            if (isset($array[$key])) {
                if ($count < 2) {
                    unset($array[$key]);
                } else {
                    $array =& $array[$key];
                }
            }
        }
    }

    /**
     * @see http://www.php.net/manual/en/function.array-walk.php
     * @return array
     */
    public static function columnize($array, $key, $index = null)
    {
        return array_column($array, $key, $index);
    }

    /**
     * array_pluck
     *
     * @param mixed $key
     * @param mixed $array
     *
     * @return array
     */
    public static function arrayPluck($key, array $array)
    {
        return array_map(
            function ($item) use ($key) {
                return is_object($item) ? $item->$key : $item[$key];
            },
            $array
        );
    }

    public static function arrayZip()
    {
        $args = func_get_args();
        $count = count($args);

        $out = [];

        for ($i = 0; $i < $count; $i++) {
            $out[$i] = self::arrayPluck($i, $args);
        }
        return $out;
    }

    /**
     * arrayMax
     *
     * @param array $args
     *
     * @access public
     * @return mixed
     */
    public static function arrayMax(array $args)
    {
        uasort(
            $args,
            function ($a, $b) {
                return count($a) < count($b) ? 1 : -1;
            }
        );
        return count(reset($args));
    }

    /**
     * arrayMin
     *
     * @param array $args
     *
     * @access public
     * @return mixed
     */
    public static function arrayMin(array $args)
    {
        usort(
            $args,
            function ($a, $b) {
                return count($a) < count($b) ? 1 : -1;
            }
        );
        return count(end($args));
    }

    /**
     * arrayIsList
     *
     * @param array $array
     *
     * @access public
     * @return boolean
     */
    public static function arrayIsList(array $array)
    {
        return ctype_digit(implode('', array_keys($array)));
    }

    /**
     * arrayCompact
     *
     * @param array $array
     *
     * @access public
     * @return array
     */
    public static function arrayCompact(array $array)
    {
        $out = array_filter(
            $array,
            function ($item) {
                return false !== (bool)$item;
            }
        );
        return self::arrayIsList($out) ? array_values($out) : $out;
    }

    private function __construct()
    {
    }
}
