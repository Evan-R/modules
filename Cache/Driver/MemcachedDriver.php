<?php

/**
 * This File is part of the Selene\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Driver;

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
    protected $driver;

    /**
     * __construct
     *
     * @param Memcached    $memcached  Memcached instance
     * @access public
     * @return void
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->setDriver($connection);
    }

    protected function setDriver(ConnectionInterface $connection)
    {
        if (!($driver = $connection->getDriver()) instanceof \Memcached) {
            throw new \InvalidArgumentException();
        }

        $connection->connect();

        $this->driver = $driver;
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
        if (!$has = $this->driver->get($key)) {
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
        return $this->driver->get($key);
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
        $expires = $this->getExpiryTime($expires);

        $cmp = $this->driver->getOption(Memcached::OPT_COMPRESSION);

        $this->driver->setOption(Memcached::OPT_COMPRESSION, $compressed);

        $cached = $this->driver->set($key, $data, $expires);

        $this->driver->setOption(Memcached::OPT_COMPRESSION, $cmp);

        return $cached;
    }

    /**
     * getExpiryTime
     *
     * @param mixed $expires
     *
     * @access protected
     * @return int
     */
    protected function getExpiryTime($expires)
    {
        return is_int($expires) ?
            (time() + ($expires * 60)) :
            (is_string($expires) ?
            strtotime($expires) :
            (time() + ($this->default * 60)));
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
        return $this->driver->increment($key, $value);
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
        return $this->driver->decrement($key, $value);
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
        return $this->driver->delete($key);
    }

    /**
     * delete all cached items
     *
     * @access protected
     * @return void
     */
    public function flushCache()
    {
        return $this->driver->flush();
    }
}
