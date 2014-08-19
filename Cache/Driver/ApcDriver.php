<?php

/*
 * This File is part of the Stream\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Driver;

/**
 * Class DriverAPC
 *
 * @uses Storage
 * @package Stream\Cache
 * @version 1.0
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ApcDriver extends AbstractDriver
{
    /**
     * Check if cached item exists
     *
     * @param Mixed $key
     * @return void
     */
    public function cachedItemExists($key)
    {
        return apc_exists($key);
    }

    /**
     * Retrieve cached item.
     *
     * @param string $key
     * @return mixed
     */
    public function getFromCache($key)
    {
        return apc_fetch($key);
    }

    /**
     * write data to cache
     *
     * @param String $key the cache item identifier
     * @param Mixed $data Data to be cached
     * @param Mixed $expires Integer value of the expiry time in minutes or
     * @param boolean $compressed compress data
     * unix timestamp
     * @return void
     */
    public function writeToCache($key, $data, $expires = 60, $compressed = false)
    {
        return apc_store($key, $data, $expires);
    }

    /**
     * save cached item with a long future expiry date
     *
     * @param Mixed $key
     * @param Mixed $data
     * @param boolean $compressed  compress data
     * @return void
     */
    public function saveForever($key, $data, $compressed = false)
    {
        return $this->writeToCache($key, $data, 0, $compressed);
    }

    /**
     * delete a cached item
     *
     * @param string $key
     * @return void
     */
    public function deleteFromCache($key)
    {
        return apc_delete($key);
    }

    /**
     * delete all cached items
     *
     * @return boolean
     */
    public function flushCache()
    {
        return apc_clear_cache('user');
    }

    /**
     * incrementValue
     *
     * @param string $key
     * @param int    $value
     *
     * @return int
     */
    protected function incrementValue($key, $value)
    {
        return apc_inc($key, $value);
    }

    /**
     * decrementValue
     *
     * @param string $key
     * @param int    $value
     *
     * @return int
     */
    protected function decrementValue($key, $value)
    {
        return apc_dec($key, $value);
    }
}
