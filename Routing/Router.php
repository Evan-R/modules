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
use \Selene\Components\Events\Dispatcher;
use \Selene\Components\Events\DispatcherInterface;
use \Selene\Components\Routing\Exception\RouteNotFoundException;
use \Selene\Components\Routing\Matchers\MatchContext;
use \Selene\Components\Routing\Events\RouteDispatchEvent;
use \Selene\Components\Routing\Events\RouteNotFoundEvent;
use \Selene\Components\Routing\Events\RouteFilterEvent;
use \Selene\Components\Routing\Events\RouteFilterAbortEvent;
use \Selene\Components\Routing\Events\RouterEvents as Events;
use \Selene\Components\Routing\Controller\DispatcherInterface as IControllers;
use \Selene\Components\Routing\Controller\Dispatcher as Controllers;
use \Selene\Components\Routing\RouteMatcherInterface as Matcher;

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
    private $routes;

    private $events;

    private $matcher;

    private $controllers;

    /**
     * @param Controllers $controllers
     * @param RouteCollectionInterface $routes
     * @param Matcher $matcher
     * @param DispatcherInterface $events
     */
    public function __construct(
        RouteCollectionInterface $routes,
        IControllers $controllers = null,
        Matcher $matcher = null,
        DispatcherInterface $events = null
    ) {
        $this->routes      = $routes;
        $this->controllers = $controllers ?: new Controllers;
        $this->matcher     = $matcher ?: new RouteMatcher;
        $this->events      = $events ?: new Dispatcher;
    }

    /**
     * Listens to an router event
     *
     * @param string|array $event
     * @param mixed $handler
     *
     * @return void
     */
    public function on($event, $handler)
    {
        $this->events->on($event, $hanlder);
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
     * getEvents
     *
     * @return DispatcherInterface
     */
    public function getEvents()
    {
        return $this->events;
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
            return $this->handleNotFound($request);
        }

        return $this->dispatchRoute($context);
    }

    /**
     * Calls a route by its name.
     *
     * @param mixed $route
     * @param array $context
     *
     * @access public
     * @return mixed
     */
    public function call($route, array $context = [], Request $request = null)
    {
        if (!$this->routes->has($route)) {
            throw new \InvalidArgumentException(sprintf('Route "%s" does not exist.', $route));
        }

        $context = new MatcherContext($this->routes->get($route), $context);

        $request = $request ?: Request::create();

        $context->setRequest($request);

        return $this->dispatchRoute($context);
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
        $route   = $context->getRoute();
        $request = $context->getRequest();

        // if there's a result on the event, abort the routing.
        if ($response  = $this->filterBefore($request, $route)) {
            return $response;
        }

        if (!$result = $this->controllers->dispatch($context)) {
            $this->handleNotFound(
                $request,
                sprintf('No handler found for Route "%s".', $context->getRequest()->getPathInfo())
            );
        }

        $event = new RouteFilterEvent($route, $request);
        $event->setResponse($result);

        $this->events->dispatch(Events::DISPATCHED, $event);

        // if there's a result on the event, abort the routing.
        if ($response  = $this->filterAfter($event)) {
            return $response;
        }

        return $event->getResponse();
    }

    /**
     * fireBeforeFilters
     *
     * @param Request $request
     * @param Route $route
     *
     * @return null|Response;
     */
    protected function filterBefore(Request $request, Route $route)
    {
        foreach ((array)$route->getBeforeFilters() as $filter) {

            // Listen for response
            $this->events->dispatch(
                Events::FILTER_BEFORE . '.' . $filter,
                $event = new RouteFilterEvent($route, $request)
            );

            if ($result = $event->getResponse()) {

                $event->setResponse($result);
                $this->events->dispatch(Events::ABORT, $event);

                return $event->getResponse();
            }
        }
    }

    /**
     * filterAfter
     *
     * @param RouteFilterEvent $event
     *
     * @return void
     */
    protected function filterAfter(RouteFilterEvent $event)
    {
        foreach ((array)$event->getRoute()->getAfterFilters() as $filter) {
            $this->events->dispatch(Events::FILTER_AFTER . '.' . $filter, $event);
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
        $this->events->dispatch(Events::DISPATCH, $event = $this->prepareDispatchEvent($route, $request));

        return $event;
    }

    /**
     * Fire the not found event.
     *
     * @param Request $request
     * @param string  $msg
     *
     * @access protected
     * @return void
     */
    protected function handleNotFound(Request $request, $msg = null)
    {
        $this->events->dispatch(Events::NOT_FOUND, new RouteNotFoundEvent($request));

        throw new RouteNotFoundException(
            $msg ?: sprintf('Route "%s" not found.', $request->getPathInfo()),
            null,
            404
        );
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
