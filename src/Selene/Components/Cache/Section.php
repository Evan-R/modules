<?php

/**
 * This File is part of the Selene\Components\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cache;

/**
 * @class Section
 * @package
 * @version $Id$
 */
class Section implements CacheInterface
{
    /**
     * cache
     *
     * @var mixed
     */
    protected $cache;

    /**
     * section
     *
     * @var string
     */
    protected $section;

    /**
     * __construct
     *
     * @param CacheInterface $storage
     * @param mixed $section
     *
     * @access public
     * @return mixed
     */
    public function __construct(CacheInterface $storage, $section)
    {
        $this->cache = $storage;
        $this->section = $section;
    }
    /**
     * get
     *
     * @param mixed $key
     *
     * @access public
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($value = $this->cache->get($this->getItemKey($key))) {
            return $value;
        }
        return $default;
    }

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
    public function set($key, $data, $expires = null, $compressed = false)
    {
        return $this->cache->set($this->getItemKey($key), $data, $expires, $compressed);
    }

    /**
     * Cache data forever
     *
     * @param mixed $cacheid
     * @param mixed $data
     * @param mixed $compressed
     */
    public function seal($key, $data, $compressed = false)
    {
        return $this->cache->seal($this->getItemKey($key), $data, $compressed);
    }

    /**
     * section
     *
     * @param mixed $section
     *
     * @access public
     * @return mixed
     */
    public function section($section)
    {
        return new static($this, $section);
    }

    /**
     * increment
     *
     * @param mixed $cacheid
     *
     * @access public
     * @return void
     */
    public function increment($key, $value = 1)
    {
        return $this->cache->increment($this->getItemKey($key), $value);
    }

    /**
     * decrement
     *
     * @param mixed $cacheid
     * @param int $value
     *
     * @access public
     * @return mixed
     */
    public function decrement($key, $value = 1)
    {
        return $this->cache->decrement($this->getItemKey($key), $value);
    }

    /**
     * Flush data from cache
     *
     * @param Mixed $cacheid
     * @access public
     * @return void
     */
    public function purge($key = null)
    {
        if (is_null($key)) {
            return $this->cache->increment($this->getKey());
        }
        return $this->cache->purge($this->getItemKey($key));
    }

    /**
     * Check if an item is already cached
     *
     * @param String $cacheid the cache item identifier
     * @access public
     * @return Boolean
     */
    public function has($key)
    {
        return !is_null($this->get($key));
    }

    /**
     * setDefault
     *
     * @param mixed $key
     * @param Closure $callback
     * @param mixed $expires
     * @param mixed $compressed
     *
     * @access public
     * @return mixed
     */
    public function setDefault($key, Closure $callback, $expires = null, $compressed = false)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $this->set($key, $data = $callback(), $expires, $compressed);
        return $data;
    }

    /**
     * sealDefault
     *
     * @param mixed $key
     * @param Closure $callback
     * @param mixed $compressed
     *
     * @access public
     * @return mixed
     */
    public function sealDefault($key, Closure $callback, $compressed = false)
    {
        return $this->seal($key, $callback(), $compressed);
    }

    /**
     * getKey
     *
     *
     * @access protected
     * @return mixed
     */
    protected function getKey()
    {
        return sprintf('section:%s:key', $this->section);
    }

    /**
     * getItemKey
     *
     *
     * @access protected
     * @return mixed
     */
    protected function getItemKey($key)
    {
        return sprintf('%s:%s:%s', $this->getSectionKey(), $this->section, $key);
    }

    /**
     * getSectionKey
     *
     *
     * @access protected
     * @return mixed
     */
    protected function getSectionKey()
    {
        if (is_null($key = $this->cache->get($skey = $this->getKey()))) {
            $this->cache->seal($skey, $key = rand(1, 10000));
        }

        return $key;
    }
}
