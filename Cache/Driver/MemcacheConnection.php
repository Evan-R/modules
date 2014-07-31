<?php

/**
 * This File is part of the Selene\Components\Cache\Driver package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cache\Driver;

use \Memcache;
use \RuntimeException;

/**
 * @class MemcacheConnection
 *
 * @package Selene\Components\Cache\Driver
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class MemcacheConnection implements ConnectionInterface
{
    /**
     * memcached
     *
     * @var Memcached
     * @access private
     */
    private $memcached;

    private $connected;

    /**
     * __construct
     *
     * @param Memcached $memcached
     * @access public
     * @return void
     */
    public function __construct(Memcache $memcache = null, array $servers = [])
    {
        $this->memcache = $memcache ?: new Memcache;

        $this->servers = $servers;
    }

    /**
     * connect
     *
     * @access public
     * @return boolean
     */
    public function connect()
    {
        if ($this->isConnected()) {
            return false;
        }

        $this->addServers();

        try {
            $this->memcache->getVersion();
        } catch (\Exception $e) {
            throw new RuntimeException('Cannot initialize Memcache: ' . $e->getMessage());
        }

        return $this->connected = true;
    }

    /**
     * close
     *
     * @access public
     * @return mixed
     */
    public function close()
    {
        $this->connected = false;
        return $this->memcache->close();
    }

    /**
     * isConnected
     *
     *
     * @access public
     * @return boolean
     */
    public function isConnected()
    {
        return (bool)$this->connected;
    }

    /**
     * getDriver
     *
     *
     * @access public
     * @return \Memcache
     */
    public function getDriver()
    {
        return $this->memcache;
    }

    /**
     * addServers
     *
     * @param array $servers
     *
     * @access protected
     * @return void
     */
    protected function addServers()
    {
        foreach ($this->servers as $server) {

            //if (0 > $this->memcache->getServerStatus($server['host'])) {
            //    continue;
            //}

            $this->memcache->addServer($server['host'], (int)$server['port'], true, (int)$server['weight']);
        }
    }
}
