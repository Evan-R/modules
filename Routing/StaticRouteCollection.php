<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing;

/**
 * @class StaticRouteCollection
 * @package Selene\Module\Routing
 * @version $Id$
 */
class StaticRouteCollection extends RouteCollection
{
    public function __construct(RouteCollectionInterface $collection)
    {
        $this->routes = $collection->raw();
    }

    /**
     * add
     *
     * @param Route $route
     * @param mixed $name
     * @param mixed $override
     *
     * @access public
     * @return mixed
     */
    public function add(Route $route, $name = null, $overrideName = false)
    {
        throw new \BadMethodCallException('cannot add route to a static collection');
    }

    /**
     * {@inheritdoc}
     */
    public function findByMethod($method)
    {
        $collection = parent::findByMethod($method);
        return new StaticRouteCollection($collection);
    }

    /**
     * {@inheritdoc}
     */
    protected function create()
    {
        return new RouteCollection;
    }
}
