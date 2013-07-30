<?php

/**
 * This File is part of the Stream\Cache package
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
 * @package Stream\Cache
 * @version 1.0
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class MemcachedDriver extends AbstractDriver
{
    /**
     * memcached
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
        $this->connection = $connection;
        $this->memcached  = $connection->getMemcached();
    }

    /**
     * check if cached item exists
     *
     * @param Mixed $cacheid
     * @access protected
     * @return void
     */
    public function cachedItemExists($cacheid)
    {
        if (!$has = $this->memcached->get($cacheid)) {
            return false;
        }
        return true;
    }

    /**
     * retrieve cached item
     *
     * @param Mixed $cacheid
     * @access protected
     * @return void
     */
    public function getFromCache($cacheid)
    {
        return $this->memcached->get($cacheid);
    }

    /**
     * write data to cache
     *
     * @param String $cacheid the cache item identifier
     * @param Mixed $data Data to be cached
     * @param Mixed $expires Integer value of the expiry time in minutes or
     * @param boolean $compressed compress data
     * unix timestamp
     * @access public
     * @return void
     */
    public function writeToCache($cacheid, $data, $expires = 60, $compressed = false)
    {
        $expires = is_int($expires) ?
            (time() + ($expires * 60)) :
            (is_string($expires) ?
            strtotime($expires) :
            (time() + ($this->default * 60)));

        $this->memcached->setOption(Memcached::OPT_COMPRESSION, $compressed);
        $cached = $this->memcached->set($cacheid, $data, $expires);
        $this->memcached->setOption(Memcached::OPT_COMPRESSION, false);

        return $cached;
    }

    /**
     * save cached item with a long future expiry date
     *
     * @param Mixed $cacheid
     * @param Mixed $data
     * @param boolean $compressed  compress data
     * @access public
     * @return void
     */
    public function saveForever($cacheid, $data, $compressed = false)
    {
        return $this->writeToCache($cacheid, $data, '2037-12-31', $compressed);
    }

    /**
     * increment
     *
     * @param mixed $cacheid
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
     * @param Mixed $cacheid
     * @access public
     * @return void
     */
    public function deleteFromCache($cacheid)
    {
        return $this->memcached->delete($cacheid);
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
