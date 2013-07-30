<?php

/**
 * This File is part of package name
 *
 * (c) author <email>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cache\Driver;

use Closure;

/**
 * @class Driver
 * @see DriverInterface
 * @abstract
 *
 * @package Selene\Components\Cache
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
abstract class AbstractDriver implements DriverInterface
{

    /**
     * default cached expiry time in minutes
     *
     * @var float
     * @access public
     */
    protected $default = 60;

    /**
     * itemExists
     *
     * @param Mixed $cacheid
     * @abstract
     * @access public
     * @return Booelan
     */
    abstract public function cachedItemExists($cacheid);

    /**
     * getCachedItem
     *
     * @param Mixed $cacheid
     * @abstract
     * @access public
     * @return Boolean
     */
    abstract public function getFromCache($cacheid);

    /**
     * writeToCache
     *
     * @param String $cacheid the cache item identifier
     * @param Mixed $data Data to be cached
     * @param Mixed $expires Integer value of the expiry time in minutes or
     * unix timestamp
     * @param boolean $compressed  compress data
     * @abstract
     * @access public
     * @return Boolean
     */

    abstract public function writeToCache($cacheid, $data, $expires = 60, $compressed = false);

    /**
     * deleteFromCache
     *
     * @param String $cacheid the cache item identifier
     * @abstract
     * @access public
     * @return Boolean
     */
    abstract public function deleteFromCache($cacheid);

    /**
     * flushCache
     *
     * @abstract
     * @access public
     * @return Boolean
     */
    abstract public function flushCache();

    /**
     * saveForeaver
     *
     * @param String $cacheid the cache item identifier
     * @param Mixed $data Data to be cached
     * @param boolean $compressed  compress data
     * @abstract
     * @access public
     * @return Boolean
     */
    abstract public function saveForever($cacheid, $data, $compressed = false);

    /**
     * increment
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    public function increment($key, $value)
    {
        $this->validateIncrementValue($value);
        return $this->incrementValue($key, $value);
    }

    /**
     * decrement
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    public function decrement($key, $value)
    {
        $this->validateIncrementValue($value);
        return $this->decrementValue($key, $value);
    }

    /**
     * validateIncrementValue
     *
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     * @access private
     * @return void
     */
    private function validateIncrementValue($value)
    {
        if (!is_int($value) or $value < 1) {
            throw new \InvalidArgumentException('Value must be Integer and greater that zero');
        }
    }

    /**
     * incrementValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access public
     * @abstract
     * @return mixed
     */
    abstract protected function incrementValue($key, $value);

    /**
     * decrementValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access protected
     * @abstract
     * @return mixed
     */
    abstract protected function decrementValue($key, $value);

    /**
     * test
     *
     * @access public
     * @return boolean
     */
    public function test()
    {
        return true;
    }

    /**
     * get default expiry time
     */
    public function getDefaultExpiry()
    {
        return $this->default;
    }
}
