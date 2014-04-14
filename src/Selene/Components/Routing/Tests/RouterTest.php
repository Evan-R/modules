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
use \Selene\Components\Routing\RouteCollectionInterface;

/**
 * @class RouterTest
 * @package Selene\Components\Routing\Tests
 * @version $Id$
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        return m::close();
    }

    protected function subject()
    {
        $class = $this->getSubljectClass();
        $ref = new \ReflectionClass($class);
        if ($ref->getConstructor()) {
            return $ref->newInstanceArgs($this->getArgRequirements());
        }

        return $ref->newInstace();
    }

    protected function makeSubject($subjectClass)
    {
        if ($args = $this->getArgRequirements()) {
            $instance = new \ReflectionClass($subjectClass);
            return $instance->newInstanceArgs($args);
        }
    }

    protected function beConstructedWith()
    {

    }

    protected function getSubljectClass()
    {
        return '\Selene\Components\Routing\Router';
    }

    protected function let(RouteCollectionInterface $routes, RouteMatcherInterface $matcher)
    {
        $args = func_get_args();
        $this->subjectConstructorArgs = $args;
    }


    protected function getArgRequirements()
    {
        $selfReflect = new \ReflectionClass($this);

        $cArgs = [];

        if ($selfReflect->hasMethod('let')) {
            $args = $selfReflect->getMethod('let')->getParameters();
            foreach ($args as $arg) {
                $cArgs[] = m::mock($arg->getClass());
            }
        }

        return $cArgs;
    }

    /**
     * @test
     */
    public function itSouldBeInstatiable()
    {
        $routes  = m::mock('Selene\Components\Routing\RouteCollectionInterface');
        $matcher = m::mock('Selene\Components\Routing\RouteMatcherInterface');
        $finder = m::mock('Selene\Components\Routing\Controller\ResolverInterface');

        $router = new Router($finder, $matcher);
        $this->assertInstanceOf('Selene\Components\Routing\Router', $router);
    }

    /**
     * @test
     */
    public function itShouldThrowAnExceptionOnDispatchIfNoEventsAreSet()
    {
        $router = $this->getRouter();

        try {
            $router->dispatch($this->mockRequest());
        } catch (\BadMethodCallException $e) {
            $this->assertEquals('cannot boot router, event dispatcher is not set', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail('BadMethodCallException should\'ve been thrown, instead saw ' . $e->getMessage());
        }

        $router = $this->getRouter();

        try {
            $router->boot();
        } catch (\BadMethodCallException $e) {
            $this->assertEquals('cannot boot router, event dispatcher is not set', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail('BadMethodCallException should\'ve been thrown, instead saw ' . $e->getMessage());
        }
    }


    /**
     * @test
     */
    public function itShouldThrowAnExceptionWhenARouteWasNotFound()
    {
        $request = $this->mockRequest();
        $request->shouldReceive('getRequestUri')->andReturn('/');
        $router = $this->getRouter();
        $events = $this->mockEvents();
        $router->setEventDispatcher($events);

        $this->prepareMatcher($router->getMatcher());

        $router->getMatcher()->shouldReceive('matches')->andReturn(false);

        $router->setRoutes(m::mock('Selene\Components\Routing\RouteCollectionInterface'));

        try {
            $router->dispatch($request);
        } catch (\Selene\Components\Routing\Exception\RouteNotFoundException $e) {
            $this->assertEquals('Route not found for /', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail('RouteNotFoundException should\'ve been thrown, instead saw ' . $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function itShouldDoStuff()
    {
        //$request = $this->mockRequest();
        //$request->shouldReceive('getRequestUri')->andReturn('/');
        //$router = $this->getRouter();
        //$events = $this->mockEvents();
        //$router->setEventDispatcher($events);

        //$this->prepareMatcher($router->getMatcher());

        //$route = new Route('index', '/', 'GET');

        //$router->getMatcher()->shouldReceive('matches')->andReturn(true);
        //$router->getMatcher()->shouldReceive('getMatchedRoute')->andReturn($route);

        //$router->setRoutes(m::mock('Selene\Components\Routing\RouteCollectionInterface'));
        //$router->dispatch($request);
    }


    protected function prepareMatcher($matcher)
    {

        $matcher->shouldReceive('prepareMatchers');

        $matcher->shouldReceive('onHostMatch');

        $matcher->shouldReceive('onRouteMatch');
    }
    protected function mockEvents()
    {
        $events = m::mock('Selene\Components\Events\DispatcherInterface');
        return $events;
    }

    protected function mockRequest()
    {
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        return $request;
    }

    /**
     * getRouter
     *
     * @param mixed $finder
     * @param mixed $matcher
     *
     * @access protected
     * @return mixed
     */
    protected function getRouter($finder = null, $matcher = null)
    {
        if (null === $matcher) {
            $matcher = m::mock('Selene\Components\Routing\RouteMatcherInterface');
        }

        if (null === $finder) {
            $finder = m::mock('Selene\Components\Routing\Controller\ResolverInterface');
        }

        $router = new Router($finder, $matcher);

        return $router;
    }
}
