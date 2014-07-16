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

class RouteCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassName
     */
    protected $subject;

    protected function setUp()
    {
        $this->collection = new RouteCollection;
    }

    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function routesShouldBeAddable()
    {
        $route = m::mock('\Selene\Components\Routing\Route');
        $route->shouldReceive('getName')->andReturn('app.index');
        $route->shouldReceive('collection')->with($this->collection);

        $this->collection->add($route);

        $this->assertSame($route, $this->collection->get('app.index'));
    }

    /** @test */
    public function routesSouldBeRetrieveableByMethod()
    {
        $routeA = m::mock('\Selene\Components\Routing\Route');
        $routeB = m::mock('\Selene\Components\Routing\Route');
        $routeC = m::mock('\Selene\Components\Routing\Route');

        $routeA->shouldReceive('getName')->andReturn('app.index');
        $routeB->shouldReceive('getName')->andReturn('app.foo');
        $routeC->shouldReceive('getName')->andReturn('app.bar');

        $routeA->shouldReceive('getMethods')->andReturn(['GET', 'HEAD']);
        $routeB->shouldReceive('getMethods')->andReturn(['PUT', 'PATCH']);
        $routeC->shouldReceive('getMethods')->andReturn(['GET', 'HEAD']);

        $routeA->shouldReceive('collection');
        $routeB->shouldReceive('collection');
        $routeC->shouldReceive('collection');

        $this->collection->add($routeA);
        $this->collection->add($routeB);
        $this->collection->add($routeC);

        $routes = $this->collection->findByMethod('get');

        $this->assertTrue($routes->has('app.index'));
        $this->assertFalse($routes->has('app.foo'));
        $this->assertTrue($routes->has('app.bar'));

    }

    /** @test */
    public function itShouldBeIteratable()
    {
        $route = m::mock('\Selene\Components\Routing\Route');
        $route->shouldReceive('collection');
        $route->shouldReceive('getName')->andReturn('app.index');
        $this->collection->add($route);

        foreach ($this->collection as $route) {
            return $this->assertTrue(true);
        }
        $this->fail('failure');
    }

    /** @test */
    public function itShouldReturnRawArray()
    {

        $this->assertTrue(is_array($this->collection->raw()));
    }

    /** @test */
    public function itShouldBeSerilizeable()
    {
        $this->assertInstanceOf(
            '\Selene\Components\Routing\RouteCollection',
            unserialize(serialize($this->collection))
        );
    }
}
