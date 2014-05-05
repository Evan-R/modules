<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Loader;

use \Selene\Components\Routing\RouteBuilder;

/**
 * @class PhpLoader extends ConfigLoader
 * @see ConfigLoader
 *
 * @package Selene\Components\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class PhpLoader extends RoutingLoader
{

    /**
     * load
     *
     * @param mixed $resource
     *
     * @access public
     * @return mixed
     */
    public function load($resource)
    {
        $routes = new RouteBuilder;

        include $resource;

        $this->routes->merge($routes->getRoutes());

        $this->container->addFileResource($resource);
    }

    /**
     * supports
     *
     * @param mixed $format
     *
     * @access public
     * @return boolean
     */
    public function supports($format)
    {
        return 'php' === $format;
    }
}
