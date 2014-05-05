<?php

/**
 * This File is part of the Selene\Components\Routing\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Loader;

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Config\Loader\ConfigLoader;
use \Selene\Components\Routing\RouteCollectionInterface;

/**
 * @class RoutingLoader
 * @package Selene\Components\Routing\Loader
 * @version $Id$
 */
abstract class RoutingLoader extends ConfigLoader
{
    public function __construct(ContainerInterface $container, RouteCollectionInterface $routes)
    {
        parent::__construct($container);
        $this->routes = $routes;
    }
}
