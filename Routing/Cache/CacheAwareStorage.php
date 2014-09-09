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

use \Selene\Module\Routing\RouteCollection;
use \Selene\Module\Routing\StaticRouteCollection;
use \Selene\Module\Routing\RouteCollectionInterface;
use \Selene\Module\Cache\Driver\DriverInterface as Driver;

/**
 * Route storage that uses `Selene\Module\Cache\Driver\*Driver` drivers.
 *
 * @package Selene\Module\Routing\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CacheAwareStorage implements StorageInterface
{
    /**
     * driver
     *
     * @var Driver
     */
    private $driver;

    /**
     * prefix
     *
     * @var string
     */
    private $prefix;

    /**
     * storeId
     *
     * @var string
     */
    private $storeId;

    /**
     * Constructor.
     *
     * @param Driver $driver
     * @param string $storeId
     * @param string $prefix
     */
    public function __construct(Driver $driver, $storeId = 'routing.routes', $prefix = 'selene.')
    {
        $this->driver  = $driver;
        $this->storeId = $storeId;
        $this->prefix  = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        return $this->driver->cachedItemExists($this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function write(RouteCollectionInterface $routes)
    {
        if ($this->exists()) {
            $this->purge();
        }

        $this->driver->saveForever($id = $this->getId(), $routes, true);
        $this->driver->saveForever($id.'.lastmod', time(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (!$this->driver->cachedItemExists($id = $this->getId())) {
            return new RouteCollection;
        }

        return new StaticRouteCollection($this->driver->getFromCache($id));
    }

    /**
     * purge
     *
     * @return void
     */
    public function purge()
    {
        $this->driver->deleteFromCache($id = $this->getId());
        $this->driver->deleteFromCache($id.'.lastmod');
    }

    /**
     * getLastWriteTime
     *
     * @return int
     */
    public function getLastWriteTime()
    {
        return $this->driver->getFromCache($this->getId().'.lastmod') ?: time();
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($time)
    {
        return $this->getLastWriteTime() < $time;
    }

    /**
     * getId
     *
     * @return string
     */
    private function getId()
    {
        return $this->prefix.$this->storeId;
    }
}
