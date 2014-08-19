<?php

/*
 * This File is part of the Selene\Module\Cache\Driver package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Driver;

/**
 * @class MemcacheDriver
 * @package Selene\Module\Cache\Driver
 * @version $Id$
 */
class MemcacheDriver extends MemcachedDriver
{
    protected $driver;

    public function __construct(ConnectionInterface $connection)
    {
        $this->setDriver($connection);
    }

    protected function setDriver(ConnectionInterface $connection)
    {
        if (!($driver = $connection->getDriver()) instanceof \Memcache) {
            throw new \InvalidArgumentException();
        }

        $connection->connect();

        $this->driver = $driver;
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
        $expires = $this->getExpiryTime($expires);

        $cached = $this->driver->set($key, $data, $compressed ? MEMCACHE_COMPRESSED : null, $expires);

        return $cached;
    }
}
