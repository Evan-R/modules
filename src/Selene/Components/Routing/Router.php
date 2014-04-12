<?php

namespace Selene\Components\Routing;

use \Symfony\Component\HttpFoundation\Request;
use \Selene\Components\Events\DispatcherInterface;
use \Selene\Components\Routing\Events\RouteFilterEvent;
use \Selene\Components\Routing\Events\RouteDispatchEvent;
use \Selene\Components\Routing\Exception\RouteNotFoundException;
use \Selene\Components\Routing\Controller\ResolverInterface;

/**
 * @class Router Router
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

    /**
     * events
     *
     * @var DispatcherInterface
     */
    protected $events;

    /**
     * booted
     *
     * @var mixed
     */
    protected $booted;

    /**
     * prepared
     *
     * @var boolean
     */
    protected $prepared;

    /**
     * routes
     *
     * @var Selene\Components\RouteCollectionInterface
     */
    protected $routes;

    /**
     * matcher
     *
     * @var Selene\Components\RouteCollectionInterface
     */
    protected $matcher;

    /**
     * filters
     *
     * @var mixed
     */
    protected $filters;

    protected $actionSet;

    /**
     * filters
     *
     * @var mixed
     */
    protected $filterEvents;

    /**
     * @param ResolverInterface $resolver
     * @param RouteMatcherInterface $matcher
     *
     * @access public
     * @return mixed
     */
    public function __construct(ResolverInterface $resolver, RouteMatcherInterface $matcher = null)
    {
        $this->resolver  = $resolver;
        $this->matcher   = $matcher ? : new RouteMatcher;

        $this->filters = [];
        $this->filtersEvents = [];

        $this->booted = false;
        $this->actionSet = false;
    }

    /**
     * setRoutes
     *
     * @access public
     * @return void
     */
    public function setRoutes(RouteCollectionInterface $routes)
    {
        $this->routes = $routes;
    }

    /**
     * setRoutes
     *
     * @param RouteCollectionInterface $routes
     *
     * @access public
     * @return mixed
     */
    public function setRoutesFromBuilder(RouteBuilder $builder)
    {
        $this->routes = $builder->getRoutes();
    }

    /**
     * setEventDispatcher
     *
     * @param Dispatcher $events
     *
     * @access public
     * @return void
     */
    public function setEventDispatcher(DispatcherInterface $events)
    {
        $this->events = $events;
    }

    /**
     * getRoutes
     *
     * @access public
     * @return \Selene\Components\Routing\RouteCollectionInterface
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * getMatcher
     *
     * @access public
     * @return \Selene\Components\Routing\RouteMatcherInterface
     */
    public function getMatcher()
    {
        return $this->matcher;
    }

    /**
     * registerFilter
     *
     * @param callable $filter
     *
     * @access public
     * @return mixed
     */
    public function registerFilter($name, $filter)
    {
        $this->filters[$name] = $filter;
    }

    /**
     * boot
     *
     * @param DispatcherInterface $events
     *
     * @access public
     * @return mixed
     */
    public function boot(DispatcherInterface $events = null)
    {
        if ($this->booted) {
            return;
        }

        if (null !== $events) {
            $this->setEventDispatcher($events);
        }

        if (!$this->events) {
            throw new \BadMethodCallException('cannot boot router, event dispatcher is not set');
        }

        $this->booted = true;
    }

    /**
     * registerRouteFilterEvents
     *
     * @param Route $route
     * @param array $filters
     * @param mixed $type
     *
     * @access protected
     * @return mixed
     */
    protected function registerRouteFilterEvents(Route $route, array $filters, $type)
    {
        foreach ($filters as $name => $filter) {
            if ($this->hasFilter($name)) {
                $this->events->on($this->getRouteFilterEventName($route, $type), $filter);
            }
        }
    }

    /**
     * dispatch
     *
     * @param Request $request
     *
     * @throws \BadMethodCallException if no dispatcher was set.
     * @throws RouteNotFoundException if no route was found
     * @access public
     * @return mixed
     */
    public function dispatch(Request $request)
    {
        $this->boot();

        if (!$route = $this->findRoute($request)) {
            throw new RouteNotFoundException(sprintf('Route not found for %s', $request->getRequestUri()));
        }

        $this->prepareDispatchRoute($route, $request);

        if ($result = $this->fireBeforeEvents($route, $request)) {
            $this->abortBeforeDispatch($route, $request);
            return;
        }

        $event = $this->fireRouteDispatchEvent($route, $request);

        return $event->getResponse();
    }

    /**
     * abortBeforeDispatch
     *
     * @param Route $route
     * @param Request $request
     *
     * @access protected
     * @return mixed
     */
    protected function abortBeforeDispatch(Route $route, Request $request)
    {
        $this->events->dispatch('router_abort', new RouteDispatchEvent($route, $request));
    }

    /**
     * fireAbortAfter
     *
     * @param Route $route
     * @param Request $request
     *
     * @access protected
     * @return mixed
     */
    protected function fireAbortAfter(Route $route, Request $request)
    {
        return null;
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
        $this->events->dispatch('router_dispatch', $event = new RouteDispatchEvent($route, $request));
        return $event;
    }

    /**
     * fireBeforeEvents
     *
     * @param Route $route
     * @param Request $request
     *
     * @access protected
     * @return mixed
     */
    protected function fireBeforeEvents(Route $route, Request $request)
    {
        $event = $this->getRouteFilterEventName($route, static::ROUTE_BEFORE);

        return $this->events->until($event, new RouteFilterEvent($route, $request));
    }

    /**
     * fireAfterEvents
     *
     * @param Route $route
     *
     * @access protected
     * @return mixed
     */
    protected function fireAfterEvents(Route $route, Request $request)
    {
        $event = $this->getRouteFilterEventName($route, static::ROUTE_BEFORE);

        return $this->events->until($event, new RouteFilterEvent($route, $request));
    }

    /**
     * findRoute
     *
     * @param Request $request
     *
     * @access public
     * @return mixed
     */
    public function findRoute(Request $request)
    {
        $this->prepareMatcher();

        if ($this->matcher->matches($request, $this->getRoutes())) {
            return $this->matcher->getMatchedRoute();
        }
    }

    /**
     * hasFilter
     *
     * @access public
     * @return bool
     */
    public function hasFilter($name)
    {
        return isset($this->filters[$name]);
    }

    /**
     * prepareDispatchRoute
     *
     * @param Route $route
     * @param Request $request
     *
     * @access protected
     * @return mixed
     */
    protected function prepareDispatchRoute(Route $route, Request $request)
    {
        if ($filters = $this->findRouteFilters($route, static::ROUTE_BEFORE)) {
            $this->registerRouteFilterEvents($route, $filters, static::ROUTE_BEFORE);
        }

        if ($filter = $this->findRouteFilters($route, static::ROUTE_AFTER)) {
            $this->registerRouteFilterEvents($route, $filters, static::ROUTE_AFTER);
        }

        $this->prepareAction();
    }

    /**
     * prepareAction
     *
     * @access protected
     * @return mixed
     */
    protected function prepareAction()
    {
        if ($this->actionSet) {
            return;
        }

        $this->events->on('router_dispatch', [$this, 'getControllerAction']);
    }

    /**
     * getControllerAction
     *
     * @param mixed $event
     *
     * @access protected
     * @return mixed
     */
    public function getControllerAction(RouteDispatchEvent $event)
    {
        $action = $this->resolver->find(
            $route = $event->getRoute()->getAction(),
            $event->getRequest()->getMethod()
        );

        $event->setResponse(call_user_func_array($action, $route->getParameters()));
    }


    /**
     * getRouteFilterEventName
     *
     * @param Route $route
     * @param mixed $type
     *
     * @access protected
     * @return string
     */
    protected function getRouteFilterEventName(Route $route, $type)
    {
        return $type . '.' . $route->getName();
    }

    /**
     * prepareMatcher
     *
     * @access protected
     * @return void
     */
    protected function prepareMatcher()
    {
        if ($this->prepared) {
            return;
        }

        $this->matcher->onHostMatch(function (Route $route, array $params) {
            return $this->setHostParameters($route, $params);
        });

        $this->matcher->onRouteMatch(function (Route $route, array $params) {
            return $this->setRouteParameters($route, $params);
        });

        $this->matcher->prepareMatchers();

        $this->prepared = true;
    }

    /**
     * findRouteFilter
     *
     * @param Route $route
     * @param mixed $filters
     *
     * @access protected
     * @return mixed will return a filter parameter if any or false if none
     */
    protected function findRouteFilters(Route $route, $type = self::ROUTE_BEFORE)
    {
        $currentRoute = $route;
        $method = $type === static::ROUTE_BEFORE ? 'getBeforeFilters' : 'getAfterFilters';

        while ($currentRoute) {

            if (null !== ($filters = call_user_func([$currentRoute, $method]))) {
                return $filters;
            }

            $currentRoute = $this->getRoutes()->get($currentRoute->getParent());
        }
        return false;
    }

    /**
     * setRouteParameter
     *
     * @access public
     * @return void
     */
    protected function setRouteParameters(Route $route, array $params)
    {
        $params = array_intersect_key($params, array_flip($route->getVars()));
        $route->setParams($params);
    }

    /**
     * setHostParameter
     *
     * @access protected
     * @return void
     */
    protected function setHostParameters(Route $route, array $params)
    {
        $params = array_intersect_key($params, array_flip($route->getHostVars()));
        $route->setHostParams($params);
    }
}
