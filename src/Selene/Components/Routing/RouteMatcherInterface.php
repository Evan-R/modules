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

use \Symfony\Component\HttpFoundation\Request;

/**
 * @class RouteMatcherInterface
 * @package Selene\Components\Routing
 * @version $Id$
 */
interface RouteMatcherInterface
{
    /**
     * matches
     *
     * @param Request $request
     * @param RouteCollectionInterface $routes
     *
     * @access public
     * @return mixed
     */
    public function matches(Request $request, RouteCollectionInterface $routes);
}
