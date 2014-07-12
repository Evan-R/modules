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
use \Selene\Components\Events\Dispatcher;
use \Selene\Components\TestSuite\TestCase;

/**
 * @class RouterTest
 * @package Selene\Components\Routing\Tests
 * @version $Id$
 */
class RouterTest extends TestCase
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
                m::mock('Selene\Components\Routing\Controller\Dispatcher'),
                new RouteCollection,
                m::mock('Selene\Components\Routing\RouteMatcherInterface')
            )
        );
    }

    /** @test */
    public function itShouldDispatchAGivenRequest()
    {
        $router = $this->getRouter();
        $request = $this->getRequestMock();

        $this->matcher->shouldReceive('matches')->andReturn(false);
        $this->events->shouldReceive('dispatch');

        $request->shouldReceive('getPathInfo')->andReturn('/foo');

        try {
            $router->dispatch($request);
        } catch (\RuntimeException $e) {
            // no routes set;
            $this->assertTrue(true);
        }

        $this->events->shouldReceive('dispatch')->andReturnUsing(function ($eventName, $event) {
            $this->assertSame(Router::EVENT_ROUTE_NOT_FOUND, $eventName);
            $this->assertInstanceof('Selene\Components\Routing\Events\RouteNotFoundEvent', $event);
        });

        try {
            $router->dispatch($request);
        } catch (\Selene\Components\Routing\Exception\RouteNotFoundException $e) {
            $this->assertSame('Route "/foo" not found.', $e->getMessage());

            return;
        }

        $this->giveUp();
    }

    /** @test */
    public function itIsExpectedThat()
    {
        $status = null;
        $router = $this->getRouter();
        $request = $this->getRequestMock();

        $this->matcher->shouldReceive('matches')->andReturn(false);

        //$events->on(Router::EVENT_ON_DISPATCH, function ($event) use (&$status) {
        //    var_dump('asd');
        //    $status = $event->getResponse()->getStatus();
        //});

        //$router->dispatch($request);

        //$this->assertSame(404, $status);
    }

    /**
     * getRouter
     *
     * @access protected
     * @return mixed
     */
    protected function getRouter()
    {
        $this->events = m::mock('Selene\Components\Events\DispatcherInterface');

        $router = new Router(
            $this->controllers = m::mock('Selene\Components\Routing\Controller\Dispatcher'),
            new RouteCollection,
            $this->matcher     = m::mock('Selene\Components\Routing\RouteMatcherInterface'),
            $this->events = m::mock('Selene\Components\Events\DispatcherInterface')
        );


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
