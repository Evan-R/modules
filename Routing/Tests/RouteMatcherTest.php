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

    /** @test */
    public function testTestCase()
    {
        $matcher = new RouteMatcher;

        $request = Request::create('/bar', 'GET');

        $routes = new RouteCollection();

        $routes->add($a = new Route('index', '/{foo?}', 'GET', ['_action' => 'foo']));
        $routes->add($b = new Route('user', '/user', 'GET', ['_action' => 'foo']));
        $routes->add($c = new Route('image', '/image', 'GET', ['_action' => 'foo']));

        $this->assertInstanceof(
            'Selene\Components\Routing\Matchers\MatchContext',
            $matcher->matches($request, $routes)
        );
    }

    protected function populateCollection(RouteCollectionInterface $routes, array $args)
    {
        foreach ($args as $routeArgs) {
            $routes->add(new Route($routeArgs['name'], $routeArgs['path'], $routeArgs['method']));
        }
    }
}
