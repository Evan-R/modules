<?php

/**
 * This File is part of the Selene\Module\Routing\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Traits;

use \Selene\Module\Routing\RouteBuilder;
use \Selene\Module\Routing\RouteCollectionInterface;

/**
 * @class RoutingLoaderTrait
 * @package Selene\Module\Routing\Traits
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
