<?php

/**
 * This File is part of the Stream\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Driver;

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
     * Flag a file as compressed
     */
    const C_COMPRESSED = 1;

    /**
     *
     * Flag a file as uncompressed
     */
    const C_UNCOMPRESSED = 0;

    /**
     * cachedItemExists
     *
     * @param mixed $key
     */
    public function cachedItemExists($key);

    /**
     * getCachedItem
     *
     * @param Mixed $key
     * @abstract
     * @access public
     * @return Boolean
     */
    public function getFromCache($key);

    /**
     * writeToCache
     *
     * @param String $key the cache item identifier
     * @param Mixed $data Data to be cached
     * @param Mixed $expires Integer value of the expiry time in minutes or
     * unix timestamp
     * @param boolean $compressed  compress data
     * @abstract
     * @access public
     * @return Boolean
     */
    public function writeToCache($key, $data, $expires = 60, $compressed = false);

    /**
     * deleteFromCache
     *
     * @param String $key the cache item identifier
     * @abstract
     * @access public
     * @return Boolean
     */
    public function deleteFromCache($key);

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
     * @param String $key the cache item identifier
     * @param Mixed $data Data to be cached
     * @param boolean $compressed  compress data
     * @abstract
     * @access public
     * @return Boolean
     */
    public function saveForever($key, $data, $compressed = false);

    /**
     * get default expiry time
     */
    public function getDefaultExpiry();
}
