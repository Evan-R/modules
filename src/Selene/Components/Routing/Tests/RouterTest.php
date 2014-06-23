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
use \Selene\Components\Routing\Router;
use \Selene\Components\Routing\RouteCollection;
use \Selene\Components\Routing\RouteCollectionInterface;

/**
 * @class RouterTest
 * @package Selene\Components\Routing\Tests
 * @version $Id$
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{

    protected $controllers;
    protected $matcher;
    protected $events;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            '\Selene\Components\Routing\RouterInterface',
            new Router(
                m::mock('Selene\Components\Routing\RouteMatcherInterface'),
                m::mock('Selene\Components\Routing\Controller\Dispatcher')
            )
        );
    }

    /** @test */
    public function itShouldDispatchAGivenRequest()
    {
        $router = $this->getRouter();
        $request = $this->getRequestMock();

        try {
            $router->dispatch($request);
        } catch (\RuntimeException $e) {
            // no routes set;
            $this->assertTrue(true);
        }

        $request->shouldReceive('getPathInfo')->andReturn('/foo');
        $router->setRoutes(new RouteCollection);

        $this->matcher->shouldReceive('matches')->andReturn(false);

        try {
            $router->dispatch($request);
        } catch (\Selene\Components\Routing\Exception\RouteNotFoundException $e) {
            $this->assertSame('Route "/foo" not found.', $e->getMessage());
        }

        $router->setEvents($this->events);

        $this->events->shouldReceive('dispatch')->andReturnUsing(function ($eventName, $event) {
            $this->assertSame('route_not_found', $eventName);
            $this->assertInstanceof('Selene\Components\Routing\Events\RouteNotFoundEvent', $event);
        });

        $this->assertNull($router->dispatch($request));
    }

    /**
     * getRouter
     *
     * @access protected
     * @return mixed
     */
    protected function getRouter()
    {
        $router = new Router(
            $this->matcher     = m::mock('Selene\Components\Routing\RouteMatcherInterface'),
            $this->controllers = m::mock('Selene\Components\Routing\Controller\Dispatcher')
        );

        $this->events = m::mock('Selene\Components\Events\DispatcherInterface');

        return $router;
    }

    protected function getRequestMock()
    {
        return m::mock('\Symfony\Component\HttpFoundation\Request');
    }


    protected function tearDown()
    {
        return m::close();
    }
}
