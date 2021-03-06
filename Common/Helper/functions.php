<?php

/*
 * This File is part of the Selene\Module\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace {

    if (!function_exists('vd')) {
        function vd()
        {
            return call_user_func_array('var_dump', func_get_args());
        }
    }

    if (!function_exists('vdd')) {
        function vdd()
        {
            call_user_func_array('var_dump', func_get_args());
            die;
        }
    }

    if (!function_exists('array_column')) {

        /**
         * @see http://www.php.net/manual/en/function.array-column.php
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

    if (!function_exists('clearValue')) {
        /**
         * nulls a given value in case it's an empty string
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

    if (!function_exists('clear_value')) {

        /**
         * @see clearValue
         */
        function clear_value($value)
        {
            return clearValue($value);
        }
    }
}
