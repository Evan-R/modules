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
use \Selene\Components\Routing\Events\RouterEvents as Events;
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
    protected $routes;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            '\Selene\Components\Routing\RouterInterface',
            new Router(
                m::mock('Selene\Components\Routing\RouteCollectionInterface')
            )
        );
    }

    /** @test */
    public function itShouldDispatchAGivenRequest()
    {
        $router = $this->getRouter();
        $request = $this->getRequestMock();

        $this->matcher->shouldReceive('matches')->andReturn(false);

        $request->shouldReceive('getPathInfo')->andReturn('/foo');

        $test = false;

        $this->events->shouldReceive('dispatch')->with(Events::NOT_FOUND, m::any())->andReturnUsing(function ($eventName, $event) use (&$test) {
            $test = true;
            $this->assertSame(Events::NOT_FOUND, $eventName);
            $this->assertInstanceof('Selene\Components\Routing\Events\RouteNotFoundEvent', $event);
        });

        try {
            $router->dispatch($request);
        } catch (\Selene\Components\Routing\Exception\RouteNotFoundException $e) {
            // no routes set;
            $this->assertTrue($test);

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
    }


    /** @test */
    public function itShouldFilterListenableEvents()
    {
        $router = $this->getRouter();

        $this->events->shouldReceive('on')->with(m::any(), m::any())->andReturnUsing(function ($event) use (&$events) {
            if (isset($event[0])) {
                $events[] = current($event);
            }
        });

        $events = [];

        $router->on($e1 = Events::FILTER_BEFORE.'.auth', function ($evt) {
        });

        $router->on($e2 = Events::DISPATCHED, function ($evt) {
        });

        $router->on($e3 = 'someevent', function ($evt) {
        });

        $this->assertSame([$e1, $e2], $events);
    }

    /**
     * getRouter
     *
     * @access protected
     * @return mixed
     */
    protected function getRouter()
    {
        $callback;
        $this->events = m::mock('Selene\Components\Events\DispatcherInterface');

        $this->events->shouldReceive('on')->with(Events::DISPATCHED, m::any())->andReturnUsing(function ($event, $fn) use (&$callback) {
            $callback = $fn;
        });

        $this->events->shouldReceive('dispatch')->with(Events::DISPATCHED, m::any())->andReturnUsing(function ($event) use (&$callback) {
            return $callback($event);
        });

        $router = new Router(
            $this->routes      = new RouteCollection,
            $this->controllers = m::mock('Selene\Components\Routing\Controller\Dispatcher'),
            $this->matcher     = m::mock('Selene\Components\Routing\RouteMatcherInterface'),
            $this->events
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
