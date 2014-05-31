<?php

/**
 * This File is part of the Selene\Components\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cache;

use \Closure;
use \ArrayAccess;
use \Selene\Components\Cache\Driver\DriverInterface;

/**
 * Class: Storage
 *
 * @implements CacheInterface
 * @implements ArrayAccess
 *
 * @package Selene\Components\Cache
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Storage implements CacheInterface, ArrayAccess
{
    /**
     * cache driver instance
     *
     * @var Stream\Cache\Interfaces\Driver
     */
    protected $driver;

    /**
     * Array containing already retrieved items from the caching source
     *
     * @var array
     * @access protected
     */
    protected $pool = [];

    /**
     * cache id prefix
     *
     * @var string
     * @access public
     */
    protected $prefix;

    /**
     * __construct
     *
     * @param Stream\Cache\Interfaces\Driver $cachDriver
     * @param string                         $prefix     cache id prefix
     */
    public function __construct(DriverInterface $cachDriver, $prefix = 'cache')
    {
        $this->driver = $cachDriver;
        $this->setCachePrefix($prefix);
    }

    /**
     * Check if an item is already cached
     *
     * @param String $key the cache item identifier
     * @access public
     * @return Boolean
     */
    public function has($key)
    {
        $key = $this->getCacheID($key);

        if (isset($this->pool[$key])) {
            return true;
        }

        return $this->driver->cachedItemExists($key);
    }

    public function segment($key)
    {

    }

    /**
     * Retrieves an Item from cache
     *
     * @param String $key the cache item identifier
     * @access public
     * @return Mixed the cached data or null
     */
    public function get($key, $default = null)
    {
        $key = $this->getCacheID($key);

        if (isset($this->pool[$key])) {
            return $this->pool[$key];
        }

        if ($data = $this->driver->getFromCache($key)) {
            return $data;
        }

        return $default;
    }

    /**
     * Writes an item to cache
     *
     * @param String $key     the cache item identifier
     * @param Mixed  $data    Data to be cached
     * @param Mixed  $expires Integer value of the expiry time in minutes or
     * unix timestamp
     * @param Boolean $compressed weather the data should be compresse or not
     *
     * @access public
     * @return bool
     */
    public function set($key, $data, $expires = null, $compressed = false)
    {
        $key = $this->getCacheID($key);

        if (is_null($expires)) {
            $expires = $this->driver->getDefaultExpiry();
        }

        if ($this->driver->writeToCache($key, $data, $expires, $compressed)) {
            $this->pool[$key] = $this->driver->getFromCache($key);

            return true;
        }

        return false;
    }

    /**
     * Save an item to cache with a far future expiry time (forever)
     *
     * @param string  $key        the cache item identifier
     * @param mixed   $data       Data to be cached
     * @param boolean $compressed compress data
     * @access public
     * @return boolean
     */
    public function seal($key, $data, $compressed = false)
    {
        $key = $this->getCacheID($key);

        return $this->driver->saveForever($key, $data, $compressed);
    }

    /**
     * Writes default data to cache.
     *
     * @param String $key     the cache item identifier
     * @param Mixed  $expires Integer value of the expiry time in minutes or
     * unix timestamp
     * @param Closure $callback   A callback function that returns default data
     * @param boolean $compressed compress data
     * @access public
     * @return Mixed the cached item or results of the callback
     */
    public function setDefault($key, Closure $callback, $expires = null, $compressed = false)
    {
        $this->set($key, $default = $this->execDefaultVal($key, $callback), $expires, $compressed);

        return $default;

    }

    /**
     * Writes default data to cache with a far future expiry date.
     *
     * @param String  $key        the cache item identifier
     * @param Closure $callback   A callbacl function that returns default data
     * @param boolean $compressed compress data
     * @access public
     * @return Mixed the cached item or results of the callback
     */
    public function sealDefault($key, Closure $callback, $compressed = false)
    {
        $this->seal($key, $default = $this->execDefaultVal($key, $callback), $compressed);

        return $default;
    }

    /**
     * Delete an item from cache
     *
     * If no cache id is specified, the cache will be flushed.
     *
     * @param String $key
     * @access public
     * @return Boolena
     */
    public function purge($key = null)
    {

        if (is_null($key)) {
            $this->pool = [];

            return $this->driver->flushCache();
        }

        $key = $this->getCacheID($key);

        if ($deleted = $this->driver->deleteFromCache($key)) {
            unset($this->pool[$key]);

            return $deleted;
        }

        return false;
    }

    /**
     * increment
     *
     * @param mixed $key
     * @param int $value
     *
     * @access public
     * @return void
     */
    public function increment($key, $value = 1)
    {
        if ($this->driver->increment($key = $this->getCacheID($key), $value)) {
            unset($this->pool[$key]);
        }
    }

    /**
     * decrement
     *
     * @param mixed $key
     * @param int $value
     *
     * @access public
     * @return void
     */
    public function decrement($key, $value = 1)
    {
        if ($this->driver->decrement($key = $this->getCacheID($key), $value)) {
            unset($this->pool[$key]);
        }
    }

    /**
     * section
     *
     * @param mixed $section
     *
     * @access public
     * @return mixed
     */
    public function section($section)
    {
        return new Section($this, $section);
    }

    /**
     * offsetExists
     *
     * @param Mixed $offset
     * @access public
     * @return void
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * offsetUnset
     *
     * @param Mixed $offset
     * @access public
     * @return void
     */
    public function offsetUnset($offset)
    {
        return $this->purge($offset);
    }

    /**
     * offsetSet
     *
     * @param Mixed $offset
     * @param Mixed $value
     * @access public
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset);
    }

    /**
     * offsetGet
     *
     * @param Mixed $offset
     * @access public
     * @return void
     */
    public function offsetGet($offset)
    {
        return !$this->has($offset) ?: $this->read($offset);
    }

    /**
     * getCacheID
     *
     * @param Mixed $key
     * @access protected
     * @return void
     */
    protected function getCacheID($key)
    {
        return sprintf("%s_%s", $this->prefix, $key);
    }

    /**
     * setCachePrefix
     *
     * @param Mixed $prefix
     * @access protected
     * @return void
     */
    protected function setCachePrefix($prefix = null)
    {
        $this->prefix = is_null($prefix) ? 'cached' : $prefix;
    }

    /**
     * execDefaultVal
     *
     * @param Mixed   $key
     * @param Closure $callback
     * @access protected
     * @return void
     */
    protected function execDefaultVal($key, Closure $callback)
    {
        if ($this->has($key)) {
            return $this->read($key);
        }

        return $callback();
    }
}
