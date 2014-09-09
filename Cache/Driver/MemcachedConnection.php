<?php

/*
 * This File is part of the Selene\Module\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Selene\Module\Cache\Driver;

use \Memcached;
use \RuntimeException;

/**
 * @class MemcachedConnection
 *
 * @package Selene\Module\Cache\Driver
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class MemcachedConnection implements ConnectionInterface
{
    /**
     * memcached
     *
     * @var Memcached
     */
    private $memcached;

    /**
     * servers
     *
     * @var array
     */
    private $servers;

    /**
     * Constructor.
     *
     * @param array $servers
     * @param Memcached $memcached
     */
    public function __construct(array $servers, Memcached $memcached = null)
    {
        $this->servers   = $servers;
        $this->memcached = $memcached ?: new Memcached;
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

        $this->memcached->addServers($this->servers);

        if (!$this->isConnected()) {
            throw new RuntimeException('Cannot initialize Memcached');
        }

        return true;
    }

    /**
     * close
     *
     * @access public
     * @return mixed
     */
    public function close()
    {
        // quit doesn't always close the connection
        $this->memcached->quit();

        return $this->isConnected() ? false : true;
    }

    /**
     * isConnected
     *
     * @access public
     * @return mixed
     */
    public function isConnected()
    {
        return (bool)$this->memcached->getVersion();
    }

    /**
     * getMemcached
     *
     *
     * @access public
     * @return mixed
     */
    public function getDriver()
    {
        return $this->memcached;
    }
}
