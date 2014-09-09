<?php

/*
 * This File is part of the Selene\Module\Routing\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Cache;

/**
 * @class ApcDriver
 *
 * @package Selene\Module\Routing\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ApcDriver implements DriverInterface
{
    /**
     * Construct
     *
     * @param MemcachedConnection $connection
     *
     * @access public
     * @return mixed
     */
    public function __construct($prefix = 'selene_routing_cache.')
    {
        $this->prefix = $prefix;
    }

    /**
     * has
     *
     * @param mixed $id
     *
     * @return boolean
     */
    public function has($id)
    {
        return apc_exists($this->prefix.$id);
    }

    /**
     * get
     *
     * @param mixed $id
     *
     * @return RouteCollectionInterface|null
     */
    public function get($id)
    {
        return apc_fetch($this->prefix.$id);
    }

    /**
     * put
     *
     * @param mixed $id
     * @param mixed $content
     *
     * @return void
     */
    public function put($id, $content)
    {
        apc_store($this->prefix.$id, $content);
        apc_store($this->prefix.$id.'.lastmod', time());
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
        $this->put($id, $content);
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
            apc_delete($this->prefix.$id.'.lastmod');

            return apc_delete($this->prefix.$id);
        }

        return false;
    }

    /**
     * getModTime
     *
     * @param string $id
     *
     * @return int
     */
    public function getModTime($id)
    {
        return apc_fetch($this->prefix.$id.'.lastmod') ?: time();
    }
}
