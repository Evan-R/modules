<?php

/**
 * This File is part of the Selene\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cache\Driver;

use Memcached;

/**
 * DriverMemcached
 *
 * @uses Storage
 * @package Selene\Cache
 * @version 1.0
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class MemcachedDriver extends AbstractDriver
{
    /**
     * Memcached instance
     *
     * @var Memcached
     * @access private
     */
    private $memcached;

    /**
     * __construct
     *
     * @param Memcached    $memcached  Memcached instance
     * @access public
     * @return void
     */
    public function __construct(MemcachedConnection $connection)
    {
        $this->memcached  = $connection->getMemcached();
    }

    /**
     * check if cached item exists
     *
     * @param mixed $key
     * @access protected
     * @return void
     */
    public function cachedItemExists($key)
    {
        if (!$has = $this->memcached->get($key)) {
            return false;
        }
        return true;
    }

    /**
     * retrieve cached item
     *
     * @param mixed $key
     * @access protected
     * @return void
     */
    public function getFromCache($key)
    {
        return $this->memcached->get($key);
    }

    /**
     * write data to cache
     *
     * @param String $key the cache item identifier
     * @param mixed $data Data to be cached
     * @param mixed $expires Integer value of the expiry time in minutes or
     * @param boolean $compressed compress data
     * unix timestamp
     * @access public
     * @return void
     */
    public function writeToCache($key, $data, $expires = 60, $compressed = false)
    {
        $expires = is_int($expires) ?
            (time() + ($expires * 60)) :
            (is_string($expires) ?
            strtotime($expires) :
            (time() + ($this->default * 60)));

        $this->memcached->setOption(Memcached::OPT_COMPRESSION, $compressed);
        $cached = $this->memcached->set($key, $data, $expires);
        $this->memcached->setOption(Memcached::OPT_COMPRESSION, false);

        return $cached;
    }

    /**
     * save cached item with a long future expiry date
     *
     * @param mixed $key
     * @param mixed $data
     * @param boolean $compressed  compress data
     * @access public
     * @return void
     */
    public function saveForever($key, $data, $compressed = false)
    {
        return $this->writeToCache($key, $data, '2037-12-31', $compressed);
    }

    /**
     * increment
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    protected function incrementValue($key, $value)
    {
        return $this->memcached->increment($key, $value);
    }

    /**
     * incrementValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    protected function decrementValue($key, $value)
    {
        return $this->memcached->decrement($key, $value);
    }

    /**
     * delete a cached item
     *
     * @param mixed $key
     * @access public
     * @return void
     */
    public function deleteFromCache($key)
    {
        return $this->memcached->delete($key);
    }

    /**
     * delete all cached items
     *
     * @access protected
     * @return void
     */
    public function flushCache()
    {
        return $this->memcached->flush();
    }
}
