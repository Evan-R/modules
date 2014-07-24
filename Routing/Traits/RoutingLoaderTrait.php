<?php

/**
 * This File is part of the Selene\Components\Routing\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Traits;

use \Selene\Components\Routing\RouteBuilder;
use \Selene\Components\Routing\RouteCollectionInterface;

/**
 * @class RoutingLoaderTrait
 * @package Selene\Components\Routing\Traits
 * @version $Id$
 */
trait RoutingLoaderTrait
{
    private $rotues;

    private $builder;

    protected function newBuilder(RouteCollectionInterface $routes)
    {
        $this->routes = new RouteBuilder($routes);
    }

    protected function getRoutes()
    {
        return $this->routes;
    }
}
