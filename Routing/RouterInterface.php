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

use \Symfony\Component\HttpFoundation\Request;
use \Selene\Module\Events\DispatcherInterface;

/**
 * @interface RouterInterface
 *
 * @package Selene\Module\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface RouterInterface
{
    //public function setRoutes(RouteCollectionInterface $routes);

    //public function getRoutes();

    //public function getMatcher();

    public function dispatch(Request $request, $type);

    //public function findRoute(Request $request);

    //public function registerFilter($name, $filter);

    //public function boot();
}
