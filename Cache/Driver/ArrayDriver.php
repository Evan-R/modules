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

use ArrayObject;

/**
 * Class DriverRuntime
 *
 * @uses Storage
 * @package Stream\Cache
 * @version 1.0
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ArrayDriver extends AbstractDriver
{
    /**
     * storage
     *
     * @var ArrayObject
     */
    protected $storage;

    /**
     * persist
     *
     * @var bool
     */
    protected $persist;

    /**
     * persistPath
     *
     * @var string
     */
    protected $persistPath;

    /**
     * @param mixed $persist
     * @param string $path
     *
     * @access public
     */
    public function __construct($persist = false, $path = '')
    {
        $this->persist     = $persist;
        $this->persistPath = $path;
        $this->setUpStorage();
    }

    /**
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->persistStorage();
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
        return isset($this->storage[$key]);
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
        if ($this->cachedItemExists($key)) {
            return $this->storage[$key];
        }
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
        $this->storage[$key] = $data;
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
        return $this->writeToCache($key, $data, 0, $compressed);
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
        unset($this->storage[$key]);
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
        $this->persistStorage();
    }

    /**
     * incrementValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access protected
     * @return mixed
     */
    protected function incrementValue($key, $value)
    {
        return $this->storage[$key] = $this->storage[$key] + $value;
    }

    /**
     * decrementValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access protected
     * @return mixed
     */
    protected function decrementValue($key, $value)
    {
        return $this->storage[$key] = $this->storage[$key] - $value;
    }

    /**
     * setUpStorage
     *
     *
     * @access private
     * @return mixed
     */
    private function setUpStorage()
    {
        if ($this->persist and file_exists($this->persistPath)) {
            try {
                $this->storage = unserialize(file_get_contents($this->persistPath));
            } catch (\Exception $e) {
                $this->storage = new ArrayObject;
            }
            return;
        }
        $this->storage = new ArrayObject;
    }

    /**
     * persistStorage
     *
     *
     * @access private
     * @return mixed
     */
    private function persistStorage()
    {
        if ($this->persist) {
            file_put_contents($this->persistPath, serialize($this->storage), LOCK_EX);
        }
    }
}
