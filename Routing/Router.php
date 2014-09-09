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
use \Selene\Module\Common\Helper\StringHelper;
use \Selene\Module\Events\Dispatcher;
use \Selene\Module\Events\EventInterface;
use \Selene\Module\Events\DispatcherInterface;
use \Selene\Module\Routing\Exception\RouteNotFoundException;
use \Selene\Module\Routing\Matchers\MatchContext;
use \Selene\Module\Routing\Event\RouteDispatched;
use \Selene\Module\Routing\Event\RouteNotFound;
use \Selene\Module\Routing\Event\RouteFilter;
use \Selene\Module\Routing\Event\RouteMatched;
use \Selene\Module\Routing\Event\RouteFilterAbort;
use \Selene\Module\Routing\Event\RouterEvents as Events;
use \Selene\Module\Routing\Controller\DispatcherInterface as ControllerDispatcher;
use \Selene\Module\Routing\Controller\Dispatcher as Controllers;
use \Selene\Module\Routing\RouteMatcherInterface as Matcher;
use \Selene\Module\Routing\Filter\FilterLoader;
use \Selene\Module\Routing\Filter\FilterInterface;
use \Selene\Adapter\Kernel\KernelInterface;
use \Selene\Adapter\Kernel\Event\KernelEvents;
use \Selene\Adapter\Kernel\Subscriber\KernelSubscriber;

/**
 * @class Router implements RouterInterface
 * @see RouterInterface
 *
 * @package Selene\Module\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Router implements RouterInterface, KernelSubscriber
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
     * filterPrefix
     *
     * @var FilterLoader
     */
    private $filters;

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
        DispatcherInterface $events = null,
        FilterLoader $filters = null
    ) {
        $this->routes      = $routes;
        $this->controllers = $controllers ?: new Controllers;
        $this->matcher     = $matcher ?: new RouteMatcher;
        $this->events      = $events ?: new Dispatcher;

        $this->dispatched  = new \SplStack;

        $this->filters = $filters ?: new FilterLoader([], $this->events);
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
     * getCurrentRoute
     *
     * @return Route|null
     */
    public function getCurrentRoute()
    {
        return $this->dispatched->count() ? $this->dispatched->top() : null;
    }

    /**
     * addFilter
     *
     * @param FilterInterface $filter
     *
     * @return void
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters->add($filter);
    }

    /**
     * setRoutes
     *
     * @param RouteCollectionInterface $routes
     *
     * @return void
     */
    //public function setRoutes(RouteCollectionInterface $routes)
    //{
        //$this->routes = $routes;
    //}


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
     * @param $type   $string
     *
     * @return void
     */
    public function dispatch(Request $request, $type)
    {
        if (!$context = $this->matcher->matches($request, $this->getRoutes(), $type)) {
            return $this->handleNotFound($request);
        }

        try {
            $this->events->dispatch(Events::MATCHED, new RouteMatched($context));
        } catch (FilterException $e) {
            var_dump($e->getMessage());
            return $e->getResponse();
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

        $this->dispatched->push($context->getRouteName());

        //$this->events->dispatch(Events::DISPATCHED, $event = new RouteDispatched($context));

        $event = new RouteDispatched($context);

        if ($result = $this->controllers->dispatch($context, $event)) {
            $event->setResponse($result);
        }

        // if the controller returns no result, or there's no controller, or
        // the event is not populated with a resoponse, handle the route not
        // found:
        if (null === ($result = $event->getResponse())) {
            $this->handleNotFound(
                $request,
                sprintf('No handler found for Route "%s".', $context->getRequest()->getPathInfo())
            );
        }

        $event = new RouteFilter($route, $request);
        $event->setResponse($result);

        $this->dispatched->pop();

        return $event->getResponse();
    }

    /**
     * getSubscriptions
     *
     * @return void
     */
    public function getSubscriptions()
    {
        return [
            KernelEvents::FILTER_REQUEST, 'onFilterRequest',
            KernelEvents::HANDLE, 'onHandleRequest'
        ];
    }

    /**
     * subscribeToKernel
     *
     * @param KernelInterface $kernel
     *
     * @return void
     */
    public function subscribeToKernel(KernelInterface $kernel)
    {
        $kernel->getEvents()->addSubscriber($this);
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

            $event = new RouteFilter($route, $request);
            // Listen for response
            $this->filters->run($filter, FilterInterface::T_BEFORE, $event);

            if ($result = $event->getResponse()) {

                $event->setResponse($result);
                $this->events->dispatch(Events::ABORT, $event);

                return $event->getResponse();
            } elseif ($event->isPropagationStopped()) {
                return $result ?: '__EMPTY__, remove this';
            }
        }
    }

    /**
     * filterAfter
     *
     * @param RouteFilter $event
     *
     * @return void
     */
    protected function filterAfter(RouteFilter $event)
    {
        foreach ((array)$event->getRoute()->getAfterFilters() as $filter) {
            $this->filters->run($filter, FilterInterface::T_AFTER, $event);
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
        $this->events->dispatch(Events::NOT_FOUND, new RouteNotFound($request));

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
            return 0 === strpos($event, Events::PREFIX);
        });
    }
}
