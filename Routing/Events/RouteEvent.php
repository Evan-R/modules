<?php

/**
 * This File is part of the Selene\Components\Routing\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Events;

use \Selene\Components\Events\Event;
use \Selene\Components\Routing\Route;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class RouteEvent
 * @package Selene\Components\Routing\Events
 * @version $Id$
 */
abstract class RouteEvent extends Event
{

    private $route;

    private $request;

    /**
     * @param Route $route
     * @param mixed $
     * @param mixed $requtest
     *
     * @access public
     */
    public function __construct(Route $route, Request $request)
    {
        $this->route = $route;
        $this->request = $request;
    }

    /**
     * getRoute
     *
     * @access public
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * getRequest
     *
     * @access public
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
