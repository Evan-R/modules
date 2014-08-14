<?php

/**
 * This File is part of the Selene\Module\Routing\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Events;

use \Selene\Module\Events\Event;
use \Selene\Module\Routing\Route;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class RouteEvent
 * @package Selene\Module\Routing\Events
 * @version $Id$
 */
abstract class RouteEvent extends Event
{

    private $route;

    private $request;

    /**
     * Constructor.
     *
     * @param Route $route
     * @param mixed $requtest
     */
    public function __construct(Route $route, Request $request)
    {
        $this->route = $route;
        $this->request = $request;
    }

    /**
     * getRoute
     *
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * getRequest
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
