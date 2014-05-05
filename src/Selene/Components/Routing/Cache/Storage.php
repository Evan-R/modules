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
use \Selene\Components\Filesystem\Traits\FsHelperTrait;

/**
 * @class Storage Storage
 *
 * @package Selene\Components\Routing\Cache
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Storage
{
    use FsHelperTrait;

    public function __construct($path)
    {
        $this->path = $path;
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
        file_put_contents($this->path, serialize($routes));
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
        if (!is_file($this->path)) {
            return new RouteCollection;
        }

        $routes = unserialize(file_get_contents($this->path));
        return new StaticRouteCollection($routes);
    }
}
