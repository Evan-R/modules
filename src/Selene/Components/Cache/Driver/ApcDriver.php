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
 * Class DriverAPC
 *
 * @uses Storage
 * @package Stream\Cache
 * @version 1.0
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ApcDriver extends Driver
{

    /**
     * check if cached item exists
     *
     * @param Mixed $cacheid
     * @access protected
     * @return void
     */
    public function cachedItemExists($cacheid)
    {
        return apc_exists($cacheid);
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
        return apc_fetch($cacheid);
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
        apc_store($cacheid, $data, $expires);
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
        apc_delete($cacheid);
    }

    /**
     * delete all cached items
     *
     * @access protected
     * @return void
     */
    public function flushCache()
    {
        apc_clear_cache('user');
    }
}

