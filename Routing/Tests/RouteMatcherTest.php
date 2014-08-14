<?php

/**
 * This File is part of the Selene\Module\Routing\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Tests;

use \Mockery as m;
use \Selene\Module\Routing\Route;
use \Selene\Module\Routing\RouteCollection;
use \Selene\Module\Routing\RouteCollectionInterface;
use \Selene\Module\Routing\RouteMatcher;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class RouteMatcherTest
 * @package Selene\Module\Routing\Tests
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
            'Selene\Module\Routing\Matchers\MatchContext',
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
