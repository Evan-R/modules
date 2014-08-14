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
use \Selene\Module\Events\Dispatcher;
use \Selene\Module\Events\EventInterface;
use \Selene\Module\Events\DispatcherInterface;
use \Selene\Module\Routing\Exception\RouteNotFoundException;
use \Selene\Module\Routing\Matchers\MatchContext;
use \Selene\Module\Routing\Events\RouteDispatchEvent;
use \Selene\Module\Routing\Events\RouteNotFoundEvent;
use \Selene\Module\Routing\Events\RouteFilterEvent;
use \Selene\Module\Routing\Events\RouteFilterAbortEvent;
use \Selene\Module\Routing\Events\RouterEvents as Events;
use \Selene\Module\Routing\Controller\DispatcherInterface as ControllerDispatcher;
use \Selene\Module\Routing\Controller\Dispatcher as Controllers;
use \Selene\Module\Routing\RouteMatcherInterface as Matcher;
use \Selene\Module\Common\Helper\StringHelper;

/**
 * @class Router implements RouterInterface
 * @see RouterInterface
 *
 * @package Selene\Module\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Router implements RouterInterface
{
    /**
     * routes
     *
     * @var RouteCollectionInterface
     */
    private $routes;

    /**
     * events
     *
     * @var \Selene\Module\Events\DispatcherInterface
     */
    private $events;

    /**
     * matcher
     *
     * @var \Selene\Module\Routing\Matchers\MatcherInterface
     */
    private $matcher;

    /**
     * controllers
     *
     * @var \Selene\Module\Routing\Controller\DispatcherInterface
     */
    private $controllers;

    /**
     * @param Controllers $controllers
     * @param RouteCollectionInterface $routes
     * @param Matcher $matcher
     * @param DispatcherInterface $events
     */
    public function __construct(
        RouteCollectionInterface $routes,
        ControllerDispatcher $controllers = null,
        Matcher $matcher = null,
        DispatcherInterface $events = null
    ) {
        $this->routes      = $routes;
        $this->controllers = $controllers ?: new Controllers;
        $this->matcher     = $matcher ?: new RouteMatcher;
        $this->events      = $events ?: new Dispatcher;

        $this->initControllerHandler();
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
        $this->events->on($this->filterEvents((array)$event), $handler);
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
        if (!$context = $this->matcher->matches($request, $this->routes)) {
            return $this->handleNotFound($request);
        }

        return $this->dispatchRoute($context);
    }

    /**
     * Dispatch events on the route retreival.
     *
     * @param Route $route the matched route.
     *
     * @return Response|mixed
     */
    public function dispatchRoute(MatchContext $context)
    {
        $route   = $context->getRoute();
        $request = $context->getRequest();

        // if there's a result on the event, abort the routing.
        if ($response  = $this->filterBefore($request, $route)) {

            return $response;
        }

        $this->events->dispatch(Events::DISPATCHED, $event = new RouteDispatchEvent($context));

        // if the controller returns no result, or there's no controller, or
        // the event is not populated with a resoponse, handle the route not
        // found:
        if (null === ($result = $event->getResponse())) {
            $this->handleNotFound(
                $request,
                sprintf('No handler found for Route "%s".', $context->getRequest()->getPathInfo())
            );
        }

        $event = new RouteFilterEvent($route, $request);
        $event->setResponse($result);

        // if there's a result on the event, abort the routing.
        if ($response  = $this->filterAfter($event)) {
            return $response;
        }

        return $event->getResponse();
    }

    /**
     * initControllerHandler
     *
     * @return void
     */
    protected function initControllerHandler()
    {
        $this->events->on(Events::DISPATCHED, function (EventInterface $event) {
            if ($result = $this->controllers->dispatch($event->getContext(), $event)) {
                $event->setResponse($result);
            }
        });
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

            $event = new RouteFilterEvent($route, $request);
            // Listen for response
            $this->events->dispatch(Events::FILTER_BEFORE . '.' . $filter, $event);

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
     * Fire the not found event.
     *
     * @param Request $request
     * @param string  $msg
     *
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
     * filterEvents
     *
     * @param array $events
     *
     * @return array
     */
    private function filterEvents(array $events)
    {
        return array_filter($events, function ($event) {
            return 0 === strpos($event, 'router.route_');
        });
    }
}
