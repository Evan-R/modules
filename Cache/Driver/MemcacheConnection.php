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

use \Memcache;
use \RuntimeException;

/**
 * @class MemcacheConnection
 *
 * @package Selene\Module\Cache\Driver
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
     * Constructor.
     *
     * @param array $servers
     * @param Memcached $memcached
     */
    public function __construct(array $servers, Memcache $memcache = null)
    {
        $this->servers = $servers;
        $this->memcache = $memcache ?: new Memcache;
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
