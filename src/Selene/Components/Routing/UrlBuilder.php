<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing;

/**
 * @class UrlBuilder
 * @package Selene\Components\Routing
 * @version $Id$
 */
class UrlBuilder
{
    /**
     * routes
     *
     * @var \Selene\Components\Routing\RouteCollectionInterface
     */
    private $routes;

    /**
     * @param RouteCollectionInterface $routs
     *
     * @access public
     */
    public function __construct(RouteCollectionInterface $routs)
    {
        $this->routes = $routes;
    }

    /**
     * path
     *
     * @param mixed $name
     * @param array $parameters
     *
     * @access public
     * @return string
     */
    public function path($name, array $parameters = [])
    {
        if (!$route = $this->routes->get($name)) {
            throw new \InvalidArgumentException(sprintf('route %s not found', $name));
        }

        $this->setRouteParameters($route, $parameters);
    }

    /**
     * setRouteParameters
     *
     * @param Route $route
     * @param array $parameters
     *
     * @access protected
     * @return mixed
     */
    protected function setRouteParameters(Route $route, array $parameters)
    {
        return null;
    }
}
