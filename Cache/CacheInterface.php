<?php

/*
 * This File is part of the Stream\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache;

use \Closure;

/**
 * Interface: InterfaceCache
 * @package Stream\Cache
 * @version 1.0
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface CacheInterface
{
    const COMPRESSED = true;

    const UNCOMPRESSED = false;

    /**
     * Check if an item is already cached
     *
     * @param string $key the cache item identifier.
     *
     * @return boolean
     */
    public function has($key);

    /**
     * Retreive cached data by id.
     *
     * @param string $key    the item key.
     * @param mixed $default the default value to return if nothing is found.
     *
     * @return mixed|null The cached data or null if no object was found
     */
    public function get($key, $default = null);

    /**
     * Write data to cache.
     *
     * @param string   $key
     * @param Mixed    $data the data to be cached
     * @param mixed    $expires a valid Date expression
     * @param boolean  $compressed
     *
     * @return boolean true on success, false on error
     */
    public function set($key, $data, $expires = null, $compressed = false);

    /**
     * Cache data with a far future expiry time.
     *
     * @param string  $key
     * @param mixed   $data
     * @param boolean $compressed
     *
     * @return void
     */
    public function seal($key, $data, $compressed = false);

    /**
     * increment
     *
     * @param string $key
     * @param int $value
     *
     * @return void
     */
    public function increment($key, $value = 1);

    /**
     * decrement
     *
     * @param string $key
     * @param int $value
     *
     * @return mixed
     */
    public function decrement($key, $value = 1);

    /**
     * Flush data from cache
     *
     * @param string $key
     * @return void
     */
    public function purge($key = null);

    /**
     * setDefault
     *
     * @param string $key
     * @param Closure $callback
     * @param mixed $expires
     * @param boolean $compressed
     *
     * @return void
     */
    public function setDefault($key, Closure $callback, $expires = null, $compressed = false);

    /**
     * sealDefault
     *
     * @param string $key
     * @param Closure $callback
     * @param boolean $compressed
     *
     * @return void
     */
    public function sealDefault($key, Closure $callback, $compressed = false);
}
