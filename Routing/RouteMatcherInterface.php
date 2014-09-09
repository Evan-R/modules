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

use \Selene\Routing\Matchers\MatchContext;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class RouteMatcherInterface
 * @package Selene\Module\Routing
 * @version $Id$
 */
interface RouteMatcherInterface
{
    /**
     * matches
     *
     * @param Request $request
     * @param RouteCollectionInterface $routes
     * @param string $type the request type
     *
     * @return Matchcontext
     */
    public function matches(Request $request, RouteCollectionInterface $routes, $type);
}
