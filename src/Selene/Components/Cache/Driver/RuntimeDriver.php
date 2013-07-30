<?php

/**
 * This File is part of the Stream\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Stream\Cache\Driver;

/**
 * Class DriverRuntime
 *
 * @uses Storage
 * @package Stream\Cache
 * @version 1.0
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class RuntimeDriver extends Driver
{
    protected $storage;

    public function __construct()
    {
        $this->storage = new \ArrayObject;
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
        return isset($this->storage[$cacheid]);
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
        if ($this->cachedItemExists($cacheid)) {
            return $this->storage[$cacheid];
        }
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
    public function writeToCache($cacheid, $data, $expires = 60, $compressed)
    {
        $this->storage[$cacheid] = $data;
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
    public function saveForever($cacheid, $data, $compressed)
    {
        return $this->writeToCache($cacheid, $data, 0, $compressed);
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
        unset($this->storage[$cacheid]);
    }

    /**
     * delete all cached items
     *
     * @access protected
     * @return void
     */
    public function flushCache()
    {
        unset($this->storage);
        $this->storage = new \ArrayObject;
    }
}

