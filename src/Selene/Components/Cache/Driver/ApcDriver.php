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
     * @access public
     */
    public function __construct()
    {
        $this->apcu = extension_loaded('apcu');
    }

    /**
     * check if cached item exists
     *
     * @param Mixed $key
     * @access protected
     * @return void
     */
    public function cachedItemExists($key)
    {
        return $this->hasApcu() ? apcu_exists($key) : apc_exists($key);
    }

    /**
     * retrieve cached item
     *
     * @param Mixed $key
     * @access protected
     * @return void
     */
    public function getFromCache($key)
    {
        return $this->hasApcu() ? apcu_fetch($key) : apc_fetch($key);
    }

    /**
     * write data to cache
     *
     * @param String $key the cache item identifier
     * @param Mixed $data Data to be cached
     * @param Mixed $expires Integer value of the expiry time in minutes or
     * @param boolean $compressed compress data
     * unix timestamp
     * @access public
     * @return void
     */
    public function writeToCache($key, $data, $expires = 60, $compressed = false)
    {
        $this->hasApcu() ? apcu_store($key, $data, $expires) : apc_store($key, $data, $expires);
    }

    /**
     * save cached item with a long future expiry date
     *
     * @param Mixed $key
     * @param Mixed $data
     * @param boolean $compressed  compress data
     * @access public
     * @return void
     */
    public function saveForever($key, $data, $compressed = false)
    {
        return $this->writeToCache($key, $data, 0, $compressed);
    }

    /**
     * delete a cached item
     *
     * @param Mixed $key
     * @access public
     * @return void
     */
    public function deleteFromCache($key)
    {
        $this->hasApcu() ? apcu_delete($key) : apc_delete($key);
    }

    /**
     * delete all cached items
     *
     * @access protected
     * @return void
     */
    public function flushCache()
    {
        $this->hasApcu() ? apcu_clear_cache('user') : apc_clear_cache('user');
    }

    /**
     * incrementValue
     *
     * @param string $key
     * @param int    $value
     *
     * @access protected
     * @return void
     */
    protected function incrementValue($key, $value)
    {
        $this->hasApcu() ? apcu_inc($key, $value) : apc_inc($key, $value);
    }

    /**
     * decrementValue
     *
     * @param string $key
     * @param int    $value
     *
     * @access protected
     * @return void
     */
    protected function decrementValue($key, $value)
    {
        $this->hasApcu() ? apcu_dec($key, $value) : apc_dec($key, $value);
    }

    /**
     * hasApcu
     *
     * @access private
     * @return boolean
     */
    private function hasApcu()
    {
        return $this->apcu;
    }
}
