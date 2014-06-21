<?php

/**
 * This File is part of the Stream\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cache;

use \Closure;

/**
 * Interface: InterfaceCache
 * @package Stream\Cache
 * @version 1.0
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface CacheInterface
{
    const COMPRESSED = true;

    const UNCOMPRESSED = false;

    /**
     * Retreive cached data by id
     *
     *
     * @param String $cacheid
     * @access public
     * @return Mixed
     * The cached data/object/array or null if no object was found
     */
    public function get($key, $default = null);

    /**
     * Write data to cache
     *
     * @param String $cacheid
     * @param Mixed  $data the data to be cached
     * @param string $expires a valid Date expression
     * @param mixed  $compressed
     * @access public
     * @return Boolean
     */
    public function set($key, $data, $expires = null, $compressed = false);

    /**
     * Cache data forever
     *
     * @param mixed $cacheid
     * @param mixed $data
     * @param mixed $compressed
     */
    public function seal($key, $data, $compressed = false);

    /**
     * increment
     *
     * @param mixed $cacheid
     *
     * @access public
     * @return void
     */
    public function increment($cacheid, $value = 1);

    /**
     * decrement
     *
     * @param mixed $cacheid
     * @param int $value
     *
     * @access public
     * @return mixed
     */
    public function decrement($cacheid, $value = 1);

    /**
     * Flush data from cache
     *
     * @param Mixed $cacheid
     * @access public
     * @return void
     */
    public function purge($key = null);


    /**
     * Check if an item is already cached
     *
     * @param String $cacheid the cache item identifier
     * @access public
     * @return Boolean
     */
    public function has($key);

    /**
     * setDefault
     *
     * @param mixed $key
     * @param Closure $callback
     * @param mixed $expires
     * @param mixed $compressed
     *
     * @access public
     * @return void
     */
    public function setDefault($key, Closure $callback, $expires = null, $compressed = false);

    /**
     * sealDefault
     *
     * @param mixed $key
     * @param Closure $callback
     * @param mixed $compressed
     *
     * @access public
     * @return void
     */
    public function sealDefault($key, Closure $callback, $compressed = false);
}
