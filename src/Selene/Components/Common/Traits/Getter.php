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
     * getDefault
     *
     * @access protected
     * @return mixed
     */
    protected function getDefault(array &$resource, $attribute, $default = null)
    {
        if (isset($resource[$attribute])) {
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
    protected function getDefaultArray(array &$resource, $attribute, $default = null)
    {
        return arrayGet($resource, $attribute) ?: $default;
    }

    /**
     * getDefaultUsingKey
     *
     * @param array $resource
     * @param mixed $attribute
     * @param mixed $default
     *
     * @access protected
     * @return mixed
     */
    protected function getDefaultUsingKey(array &$resource, $attribute, $default = null)
    {
        if ($this->hasKey($resource, $attribute)) {
            return $resource[$attribute];
        }
        return $default;
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
