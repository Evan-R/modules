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
use \Selene\Components\Events\DispatcherInterface;
use \Selene\Components\Routing\Exception\RouteNotFoundException;
use \Selene\Components\Routing\Matchers\MatchContext;
use \Selene\Components\Routing\Controller\Dispatcher as ControllerDispatcher;
use \Selene\Components\Routing\Events\RouteDispatchEvent;
use \Selene\Components\Routing\Events\RouteNotFoundEvent;
use \Selene\Components\Routing\Events\RouteFilterEvent;
use \Selene\Components\Routing\Events\RouteFilterAbortEvent;

/**
 * @class Router implements RouterInterface
 * @see RouterInterface
 *
 * @package Selene\Components\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Router implements RouterInterface
{
    const ROUTE_BEFORE  = 'route_before';

    const ROUTE_AFTER   = 'route_after';

    const FILTER_BEFORE = 'filter_before';

    const FILTER_AFTER  = 'filter_after';

    private $routes;

    private $events;

    private $matcher;

    private $dispatcher;

    /**
     * @param RouteMatcherInterface $matcher
     * @param Dispatcher $events
     *
     * @access public
     */
    public function __construct(RouteMatcherInterface $matcher, ControllerDispatcher $dispatcher)
    {
        $this->matcher    = $matcher;
        $this->dispatcher = $dispatcher;
    }

    public function setEvents(DispatcherInterface $events)
    {
        $this->events = $events;
    }

    public function getEvents()
    {
        return $this->events;
    }

    /**
     * setRoutes
     *
     * @param RouteCollectionInterface $routes
     *
     * @return void
     */
    public function setRoutes(RouteCollectionInterface $routes)
    {
        $this->routes = $routes;
    }

    /**
     * getRoutes
     *
     * @access public
     * @return RouteCollectionInterface
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Dispatch a route collections agaibts a request
     *
     * @param Request $request
     *
     * @return void
     */
    public function dispatch(Request $request)
    {
        if (!$context = $this->matcher->matches($request, $this->prepareRoutes())) {
            $this->handleNotFound($request);

            return;
        }

        $this->dispatchRoute($context);
    }

    /**
     * Dispatch events on the route retreival.
     *
     * @param Route $route the matched route.
     *
     * @return void
     */
    protected function dispatchRoute(MatchContext $context)
    {
        if ($result = $this->fireBeforeFilters(
            $request = $context->getRequest(),
            $route   = $context->getRoute()
        )) {

            $this->events->dispatch('router_dispatch', $event = new RouteFilterAbortEvent($result));

            return;
        }

        if (!$result = $this->dispatcher->dispatch($context)) {
            throw new RouteNotFoundException(
                sprintf('No handler found for Route "%s".', $context->getRequest()->getPathInfo())
            );
        }

        $event = $this->fireRouteDispatchEvent($context->getRoute(), $context->getRequest());
        $event->setResponse($result);
    }

    /**
     * fireBeforeFilters
     *
     * @param Request $request
     * @param Route $route
     *
     * @return void
     */
    protected function fireBeforeFilters(Request $request, Route $route)
    {
        foreach ((array)$route->getBeforeFilters() as $filter) {
            if ($result = $this->events->dispatch(
                static::FILTER_BEFORE . '.' . $filter,
                new RouteFilterEvent($route, $request)
            )) {

                return current($result);
            }
        }
    }

    /**
     * prepareDispatchEvent
     *
     * @param Route $route
     * @param Request $request
     *
     * @access protected
     * @return mixed
     */
    protected function prepareDispatchEvent(Route $route, Request $request)
    {
        return new RouteDispatchEvent($route, $request);
    }

    /**
     * fireRouteDispatchEvent
     *
     * @param Route $route
     * @param Request $request
     *
     * @access protected
     * @return mixed
     */
    protected function fireRouteDispatchEvent(Route $route, Request $request)
    {
        $this->events->dispatch('router_dispatch', $event = $this->prepareDispatchEvent($route, $request));

        return $event;
    }

    /**
     * Fire the not found event.
     *
     * @param Request $request
     *
     * @access protected
     * @return void
     */
    protected function handleNotFound(Request $request)
    {
        if (!$this->events) {
            throw new RouteNotFoundException(
                sprintf('Route "%s" not found.', $request->getPathInfo())
            );
        }

        $this->events->dispatch('route_not_found', new RouteNotFoundEvent($request));
    }

    /**
     * prepareRoutes
     *
     * @access private
     * @throws \RuntimeException
     * @return RouteCollectionInterface
     */
    private function prepareRoutes()
    {
        if (!$routes = $this->getRoutes()) {
            throw new \RuntimeException;
        }

        return $routes;
    }
}
