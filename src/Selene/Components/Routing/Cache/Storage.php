<?php

/**
 * This File is part of the Selene\Components\Routing\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Cache;

use \Selene\Components\Routing\RouteCollection;
use \Selene\Components\Routing\StaticRouteCollection;
use \Selene\Components\Routing\RouteCollectionInterface;

/**
 * @class Storage Storage
 *
 * @package Selene\Components\Routing\Cache
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Storage implements StorageInterface
{

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var string
     */
    private $storeId;


    /**
     * @param DriverInterface $driver
     * @param string $storeId
     */
    public function __construct(DriverInterface $driver, $storeId = 'selene_routes')
    {
        $this->driver  = $driver;
        $this->storeId = $storeId;
    }

    /**
     * write
     *
     * @param RouteCollectionInterface $routes
     *
     * @access public
     * @return mixed
     */
    public function write(RouteCollectionInterface $routes)
    {
        if ($this->driver->has($this->storeId)) {
            $this->driver->replace($this->storeId, $routes);
        } else {
            $this->driver->put($this->storeId, $routes);
        }
    }

    /**
     * read
     *
     * @param mixed $data
     *
     * @access public
     * @return mixed
     */
    public function read()
    {
        if (!$this->driver->has($this->storeId)) {
            return new RouteCollection;
        }

        return new StaticRouteCollection($this->driver->get($this->storeId));
    }
}
