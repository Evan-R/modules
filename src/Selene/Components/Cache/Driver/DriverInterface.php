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

/**
 * Interface: Driver
 * @package Stream\Cache
 * @version 1.0
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface DriverInterface
{
    /**
     * cachedItemExists
     *
     * @param mixed $cacheid
     */
    public function cachedItemExists($cacheid);

    /**
     * getCachedItem
     *
     * @param Mixed $cacheid
     * @abstract
     * @access public
     * @return Boolean
     */
    public function getFromCache($cacheid);

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
    public function writeToCache($cacheid, $data, $expires = 60, $compressed = false);

    /**
     * deleteFromCache
     *
     * @param String $cacheid the cache item identifier
     * @abstract
     * @access public
     * @return Boolean
     */
    public function deleteFromCache($cacheid);


    /**
     * flushCache
     *
     * @abstract
     * @access public
     * @return Boolean
     */
    public function flushCache();

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
    public function saveForever($cacheid, $data, $compressed);

    /**
     * get default expiry time
     */
    public function getDefaultExpiry();
}
