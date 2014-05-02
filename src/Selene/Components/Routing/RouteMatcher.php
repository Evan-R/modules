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
use \Selene\Components\Routing\Matchers\MethodMacher;
use \Selene\Components\Routing\Matchers\HostMatcher;
use \Selene\Components\Routing\Matchers\RegexPathMatcher;
use \Selene\Components\Routing\Matchers\StaticPathMatcher;
use \Selene\Components\Routing\Matchers\DirectPathMatcher;

/**
 * @class RouteMatcher
 * @package Selene\Components\Routing
 * @version $Id$
 */
class RouteMatcher implements RouteMatcherInterface
{
    protected $route;

    protected $matchers;

    /**
     * routeMatchCallback
     *
     * @var mixed
     */
    protected $callbacks;

    /**
     * onMatch
     *
     * @param callable $matchCallBack
     *
     * @access public
     * @return void
     */
    public function onRouteMatch(callable $callback)
    {
        $this->callbacks['route'] = $callback;
    }

    /**
     * onHostMatch
     *
     * @param callable $matchCallBack
     *
     * @access public
     * @return void
     */
    public function onHostMatch(callable $callback)
    {
        $this->callbacks['host'] = $callback;
    }

    /**
     * prepareMatcher
     *
     * @access protected
     * @return mixed
     */
    public function prepareMatchers()
    {
        if (isset($this->callbacks['host'])) {
            $this->getHostMatcher()->onMatch($this->callbacks['host']);
        }

        if (isset($this->callbacks['route'])) {
            $this->getRegexpPathMatcher()->onMatch($this->callbacks['route']);
        }
    }

    /**
     * getMatchedRoute
     *
     * @access public
     * @return Route
     */
    public function getMatchedRoute()
    {
        return $this->route ?: false;
    }

    /**
     * matches
     *
     * @param Request $request
     * @param RouteCollectionInterface $routes
     *
     * @access public
     * @return mixed
     */
    public function matches(Request $request, RouteCollectionInterface $routes)
    {
        $this->route = null;

        //just filter routes that matches the current request method.
        $routes = $routes->findByMethod($request->getMethod());

        foreach ($routes as $route) {

            $route->compile();

            $noVars = false;
            $matchesHost = false;

            // if the static path does not match return immediately.
            if (!$this->matchStaticPath($route, $request)) {
                continue;
            // if the route has no vars and matches the statuc path, we have
            // a direct match.
            // if host does not match return immediately.
            // matcher will return true if route has no host.
            } elseif (!$matchesHost = $this->matchHost($route, $request)) {
                continue;
            } elseif ($noVars = (0 === count($route->getVars())) &&
                $matchesHost && $this->directMatch($route, $request)
            ) {
                $this->route = $route;
                return true;
            // try to match the match the path reqexp.
            } elseif ($this->matchPathRegexp($route, $request) && $matchesHost) {
                $this->route = $route;
                return true;
            }
        }

        return false;
    }

    /**
     * matchHost
     *
     * @param Route $route
     * @param MatcherInterface $matcher
     *
     * @access protected
     * @return bool
     */
    protected function matchHost(Route $route, Request $request)
    {
        return $this->getHostMatcher()->matches($route, $request->getHost());
    }

    /**
     * matchStaticPath
     *
     * @param Route $route
     * @param Request $request
     *
     * @access protected
     * @return mixed
     */
    protected function matchStaticPath(Route $route, Request $request)
    {
        return $this->getStaticPathMatcher()->matches($route, $request->getRequestUri());
    }

    /**
     * matchPathRegexp
     *
     * @param Route $route
     * @param Request $request
     *
     * @access protected
     * @return mixed
     */
    protected function matchPathRegexp(Route $route, Request $request)
    {
        return $this->getRegexpPathMatcher()->matches($route, $request->getRequestUri());
    }

    /**
     * directMatch
     *
     * @param Route $route
     * @param Request $request
     *
     * @access protected
     * @return mixed
     */
    protected function directMatch(Route $route, Request $request)
    {
        return $this->getDirectMatcher()->matches($route, $request->getRequestUri());
    }

    /**
     * getStaticPathMatcher
     *
     * @access protected
     * @return MatcherInterface
     */
    protected function getStaticPathMatcher()
    {
        if (!isset($this->matchers['static_path'])) {
            $this->matchers['static_path'] = new StaticPathMatcher;
        }

        return $this->matchers['static_path'];
    }

    protected function getDirectMatcher()
    {
        if (!isset($this->matchers['direct'])) {
            $this->matchers['direct'] = new DirectPathMatcher;
        }

        return $this->matchers['direct'];
    }

    /**
     * getRegexpPathMatcher
     *
     * @access protected
     * @return MatcherInterface
     */
    protected function getRegexpPathMatcher()
    {
        if (!isset($this->matchers['regexp_path'])) {
            $this->matchers['regexp_path'] = new RegexPathMatcher;
        }

        return $this->matchers['regexp_path'];
    }

    /**
     * getHostMatcher
     *
     * @access protected
     * @return MatcherInterface
     */
    protected function getHostMatcher()
    {
        if (!isset($this->matchers['host'])) {
            $this->matchers['host'] = new HostMatcher;
        }

        return $this->matchers['host'];
    }

    protected function getDirectPathMatcher()
    {
        if (!isset($this->matchers['direct_path'])) {
            $this->matchers['direct_path'] = new DirectPathMatcher;
        }

        return $this->matchers['direct_path'];
    }
}
