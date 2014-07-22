<?php

/**
 * This File is part of the Selene\Components\Common\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Traits;

use \Selene\Components\Common\Helper\ListHelper;

/**
 * @trait Getter
 *
 * @package Selene\Components\Common\Traits
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
trait Getter
{
    /**
     * Gets a value from a given source array.
     *
     * @param array  $resource the source array from which the attribute should
     * be retreived
     * @param string $attribute the attribite id
     * @param mixed  $default a value to retrieve if the actual attribute is not
     * found on the source array.
     *
     * @access protected
     * @return mixed retourns the value found on the source array if found,
     * otherwise the given default value which defaults to `null`
     */
    protected function getDefault(array $resource, $attribute, $default = null)
    {
        if (isset($resource[$attribute])) {
            return $resource[$attribute];
        }

        return $default;
    }

    /**
     * Gets a value from a given source array.
     *
     * @see Getter::getDefault()
     * @param array $resource
     * @param string $attribute
     * @param callable $use a callable to compute the default value to retreive,
     * in case no value was found on the input array.
     *
     * @access protected
     * @return mixed
     */
    protected function getDefaultUsing(array $resource, $attribute, callable $use)
    {
        if (!isset($resource[$attribute])) {
            return call_user_func_array($use, [$resource, $attribute]);
        }

        return $resource[$attribute];
    }

    /**
     * Gets a value from a given source array.
     *
     * This method is just like `Getter::getDefault()`, but insead of
     * evaluating if the value is set on a given attribute, it will check if
     * the given attribute key exists on the input array.
     *
     * @see \Selene\Components\Common\Traits\Getter::getDefault()
     *
     * @access protected
     * @return mixed
     */
    protected function getDefaultUsingKey(array $resource, $attribute, $default = null)
    {
        if ($this->hasKey($resource, $attribute)) {
            return $resource[$attribute];
        }
        return $default;
    }

    /**
     * getDefaultArray
     *
     * @param array $resource
     * @param mixed $attribute
     * @param mixed $default
     *
     * @access protected
     * @return mixed
     */
    protected function getDefaultArray(array $resource, $attribute, $default = null, $delimitter = '.')
    {
        return ListHelper::arrayGet($resource, $attribute, $delimitter) ?: $default;
    }

    /**
     * hasKey
     *
     * @param mixed $resource
     * @param mixed $key
     *
     * @access protected
     * @return mixed
     */
    protected function hasKey(array $resource, $key)
    {
        return array_key_exists($key, $resource);
    }
}
