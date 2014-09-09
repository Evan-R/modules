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

use \SplStack;
use \Symfony\Component\HttpFoundation\Request;
use \Selene\Module\Routing\Matchers\MethodMacher;
use \Selene\Module\Routing\Matchers\HostMatcher;
use \Selene\Module\Routing\Matchers\RegexPathMatcher;
use \Selene\Module\Routing\Matchers\StaticPathMatcher;
use \Selene\Module\Routing\Matchers\DirectPathMatcher;
use \Selene\Module\Routing\Matchers\SchemeMatcher;
use \Selene\Module\Routing\Matchers\MatchContext;
use \Selene\Module\Routing\Cache\ApcSectionCache;
use \Selene\Module\Routing\Cache\SectionCacheInterface;

/**
 * @class RouteMatcher
 * @package Selene\Module\Routing
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
     * matches
     *
     * @var SplStack
     */
    private $matches;

    /**
     * prepared
     *
     * @var boolean
     */
    private $prepared;

    /**
     * Constructor.
     */
    public function __construct(SectionCacheInterface $cache = null)
    {
        $this->cache = $cache ?: new ApcSectionCache;
        $this->matches = new SplStack;
    }

    /**
     * matches
     *
     * @param Request $request
     * @param RouteCollectionInterface $routes
     *
     * @return MatchContext
     */
    public function matches(Request $request, RouteCollectionInterface $routes, $type)
    {
        $this->prepareMatchers();

        //just filter routes that matches the current request method.
        $routes->findByMethod($request->getMethod());
        $routeName = null;

        if (0 < count($routes)) {
            $routeName = $this->findRoute($routes, $request);
        }

        return $this->getMatchContext($request, $routeName, $type);
    }

    /**
     * findRoute
     *
     * @param RouteCollectionInterface $routes
     * @param Request $request
     *
     * @return string|null
     */
    protected function findRoute(RouteCollectionInterface $routes, Request $request)
    {
        $routeName = null;
        $matchedRoute = null;

        foreach ($routes as $routeName => $route) {

            if (!$route->isCompiled()) {
                $route->setName($routeName);
                $route->compile();
            }

            $noVars = false;
            $matchesHost = false;

            // if the static path and scheme does not match return immediately.
            if (!$this->matchStaticPath($route, $request) || !$this->matchScheme($route, $request)) {
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

        return $routeName;
    }

    /**
     * prepareMatcher
     *
     * @return void
     */
    protected function prepareMatchers()
    {
        if (true === $this->prepared) {
            return;
        }

        $this->prepared = true;

        $this->getHostMatcher()->onMatch(function ($route) {
            $this->matches->push(new MatchContext($route, []));
        });

        $this->getDirectMatcher()->onMatch(function (Route $route) {
            $this->matches->push(new MatchContext($route, []));
        });

        $this->getRegexpPathMatcher()->onMatch(function (Route $route, $params = []) {
            $this->matches->push(new MatchContext($route, $params));
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
        return $this->getStaticPathMatcher()->matches($route, $request->getPathInfo());
    }

    /**
     * matchPathRegexp
     *
     * @param Route $route
     * @param Request $request
     *
     * @return mixed
     */
    protected function matchScheme(Route $route, Request $request)
    {
        return $this->getSchemeMatcher()->matches($route, $request->getScheme());
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
        return $this->getRegexpPathMatcher()->matches($route, $request->getPathInfo());
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
        return $this->getDirectMatcher()->matches($route, $request->getPathInfo());
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
     * getSchemeMatcher
     *
     * @return MatcherInterface
     */
    protected function getSchemeMatcher()
    {
        if (!isset($this->matchers['scheme'])) {
            $this->matchers['scheme'] = new SchemeMatcher;
        }

        return $this->matchers['scheme'];
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
     * @return DirectPathMatcher
     */
    protected function getDirectPathMatcher()
    {
        if (!isset($this->matchers['direct_path'])) {
            $this->matchers['direct_path'] = new DirectPathMatcher;
        }

        return $this->matchers['direct_path'];
    }

    /**
     * findRoutesBySection
     *
     * @param RouteCollectionInterface $routes
     * @param mixed $pathInfo
     *
     * @return void
     */
    protected function findRoutesBySection(RouteCollectionInterface $routes, $pathInfo)
    {
        if (null !== $this->cache && $this->cache->has($pathInfo)) {
            $collection = new RouteCollection;

            foreach ($this->cache->get($pathInfo) as $name) {
                $collection->add($routes->get($name), $name);
            }

            return $collection;
        }

        return $routes;
    }

    /**
     * @return MatchContext
     */
    private function getMatchContext(Request $request, $type, $routeName = null)
    {
        try {
            $context = $this->matches->pop();
            $context->setRequest($request);
            $context->setRequestType($type);

            if ($context->getRoute()) {
                $context->setRouteName($routeName);
            }

            return $context;
        } catch (\Exception $e) {
        }
    }
}
