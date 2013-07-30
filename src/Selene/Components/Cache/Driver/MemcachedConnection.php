<?php

/**
 * This File is part of the Selene\Components\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Selene\Components\Cache\Driver;

use Memcached;
use RuntimeException;

/**
 * @class MemcachedConnection
 *
 * @package Selene\Components\Cache\Driver
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class MemcachedConnection
{
    /**
     * memcached
     *
     * @var Memcached
     * @access private
     */
    private $memcached;

    /**
     * __construct
     *
     * @param Memcached $memcached
     * @access public
     * @return void
     */
    public function __construct(Memcached $memcached, array $servers)
    {
        $this->memcached = $memcached;
        $this->init($servers);
    }

    /**
     * init
     *
     * @param array $servers
     * @access public
     * @throws RuntimeException
     * @return Memcached
     */
    public function init(array $servers)
    {
        $this->memcached->addServers($servers);

        if ($this->memcached->getVersion() === false) {
            throw new RuntimeException('Cannot initialize memcache');
        }

        return $this->memcached;
    }

    /**
     * getMemcached
     *
     *
     * @access public
     * @return mixed
     */
    public function getMemcached()
    {
        return $this->memcached;
    }
}
