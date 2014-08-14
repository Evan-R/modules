<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Cache;

use \Memcached;
use \Selene\Module\Cache\Driver\MemcachedConnection;

/**
 * @class MemcachedDriver implements DriverInterface
 * @see DriverInterface
 *
 * @package Selene\Module\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class MemcachedDriver implements DriverInterface
{
    /**
     * memcached
     *
     * @var \Memcached
     */
    private $memcached;

    /**
     * __construct
     *
     * @param MemcachedConnection $connection
     *
     * @access public
     * @return mixed
     */
    public function __construct(MemcachedConnection $connection)
    {
        $connection->connect();

        $this->memcached = $connection->getDriver();
    }

    /**
     * put
     *
     * @param mixed $id
     * @param mixed $content
     *
     * @access public
     * @return void
     */
    public function put($id, $content)
    {
        $this->store($id, $content, 'set');
    }

    /**
     * replace
     *
     * @param mixed $id
     * @param mixed $content
     *
     * @access public
     * @return void
     */
    public function replace($id, $content)
    {
        $this->store($id, $content, 'replace');
    }

    /**
     * remove
     *
     * @param mixed $id
     *
     * @access public
     * @return boolean
     */
    public function remove($id)
    {
        if ($this->has($id)) {
            return $this->memcached->delete($id);
        }

        return false;
    }

    /**
     * get
     *
     * @param mixed $id
     *
     * @access public
     * @return mixed
     */
    public function get($id)
    {
        return $this->memcached->get($id);
    }

    /**
     * has
     *
     * @param mixed $id
     *
     * @access public
     * @return boolean
     */
    public function has($id)
    {
        return (bool)$this->memcached->get($id);
    }

    /**
     * store
     *
     * @param mixed $id
     * @param mixed $content
     * @param mixed $method
     *
     * @access private
     * @return mixed
     */
    private function store($id, $content, $method = 'set')
    {
        $cmp = $this->setCompression();

        call_user_func_array([$this->memcached, $method], [$id, $content, 0]);

        $this->resetCompression($cmp);
    }

    /**
     * setCompression
     *
     * @access private
     * @return boolean
     */
    private function setCompression()
    {
        $cmp = $this->memcached->getOption(Memcached::OPT_COMPRESSION);
        $this->resetCompression(true);

        return $cmp;
    }

    /**
     * resetCompression
     *
     * @param mixed $cmp
     *
     * @access private
     * @return void
     */
    private function resetCompression($cmp)
    {
        $this->memcached->setOption(Memcached::OPT_COMPRESSION, $cmp);
    }
}
