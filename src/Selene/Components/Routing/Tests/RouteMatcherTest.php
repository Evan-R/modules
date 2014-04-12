<?php

/**
 * This File is part of the Selene\Components\Routing\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests;

use \Mockery as m;
use \Selene\Components\Routing\Route;
use \Selene\Components\Routing\RouteCollection;
use \Selene\Components\Routing\RouteCollectionInterface;
use \Selene\Components\Routing\RouteMatcher;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class RouteMatcherTest
 * @package Selene\Components\Routing\Tests
 * @version $Id$
 */
class RouteMatcherTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function testTestCase()
    {
        $matcher = new RouteMatcher;

        $matcher->onRouteMatch(function (Route $route, array $params) use (&$i) {
            $params = array_intersect_key($params, array_flip($route->getVars()));
            $route->setParams($params);
            var_dump($route->getParams());
        });

        $matcher->prepareMatchers();

        //$request = Request::create('/bar', 'GET');
        $request = m::mock('\Symfony\Component\HttpFoundation\Request');

        $request->shouldReceive('getMethod')->andReturn('GET');
        $request->shouldReceive('getRequestUri')->andReturn('/bar');
        $request->shouldReceive('getHost')->andReturn(null);

        $routes = new RouteCollection();

        $routes->add($a = new Route('index', '/{foo?}', 'GET'));
        $routes->add($b = new Route('user', '/user', 'GET'));
        $routes->add($c = new Route('image', '/image', 'GET'));

        $this->assertTrue($matcher->matches($request, $routes));
    }

    protected function populateCollection(RouteCollectionInterface $routes, array $args)
    {
        foreach ($args as $routeArgs) {
            $routes->add(new Route($routeArgs['name'], $routeArgs['path'], $routeArgs['method']));
        }
    }
}
