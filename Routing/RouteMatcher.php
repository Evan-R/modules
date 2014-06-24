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
use \Selene\Components\Routing\Matchers\MatchContext;

/**
 * @class RouteMatcher
 * @package Selene\Components\Routing
 * @version $Id$
 */
class RouteMatcher implements RouteMatcherInterface
{
    /**
     * matchers
     *
     * @var array
     */
    private $matchers;

    /**
     * prepared
     *
     * @var boolean
     */
    private $prepared;

    private $matchContext;

    /**
     * Create new RouteMatcher instance
     */
    public function __construct()
    {
        $this->prepared = false;
    }

    /**
     * matches
     *
     * @param Request $request
     * @param RouteCollectionInterface $routes
     *
     * @return mixed
     */
    public function matches(Request $request, RouteCollectionInterface $routes)
    {
        $this->prepareMatchers();

        $matchedRoute = null;

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
                break;
            // try to match the match the path reqexp.
            } elseif ($this->matchPathRegexp($route, $request) && $matchesHost) {
                break;
            }
        }

        return $this->getMatchContext($request);
    }

    /**
     * prepareMatcher
     *
     * @return void
     */
    protected function prepareMatchers()
    {
        if ($this->prepared) {
            return;
        }

        $this->prepared = true;

        $this->getHostMatcher()->onMatch(function ($route) {
            $this->matchContext = new MatchContext($route, []);
        });

        $this->getDirectMatcher()->onMatch(function (Route $route) {
            $this->matchContext = new MatchContext($route, []);
        });

        $this->getRegexpPathMatcher()->onMatch(function (Route $route, $params = []) {
            $this->matchContext = new MatchContext($route, $params);
        });
    }

    /**
     * matchHost
     *
     * @param Route $route
     * @param MatcherInterface $matcher
     *
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
     * @return mixed
     */
    protected function directMatch(Route $route, Request $request)
    {
        return $this->getDirectMatcher()->matches($route, $request->getRequestUri());
    }

    /**
     * getStaticPathMatcher
     *
     * @return MatcherInterface
     */
    protected function getStaticPathMatcher()
    {
        if (!isset($this->matchers['static_path'])) {
            $this->matchers['static_path'] = new StaticPathMatcher;
        }

        return $this->matchers['static_path'];
    }

    /**
     * getDirectMatcher
     *
     * @return mixed
     */
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

    /**
     * getDirectPathMatcher
     *
     * @return mixed
     */
    protected function getDirectPathMatcher()
    {
        if (!isset($this->matchers['direct_path'])) {
            $this->matchers['direct_path'] = new DirectPathMatcher;
        }

        return $this->matchers['direct_path'];
    }

    /**
     * @return MatchContext
     */
    private function getMatchContext(Request $request)
    {
        if (!$context = $this->matchContext) {
            return false;
        }

        $context->setRequest($request);

        $this->matchContext = null;

        return $context;
    }
}
