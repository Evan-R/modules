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
        $route = m::mock('\Selene\Module\Routing\Route');
        $route->shouldReceive('getName')->andReturn('app.index');
        $route->shouldReceive('collection')->with($this->collection);

        $this->collection->add($route);

        $this->assertSame($route, $this->collection->get('app.index'));
    }

    /** @test */
    public function routesSouldBeRetrieveableByMethod()
    {
        $routeA = m::mock('\Selene\Module\Routing\Route');
        $routeB = m::mock('\Selene\Module\Routing\Route');
        $routeC = m::mock('\Selene\Module\Routing\Route');

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
        $route = m::mock('\Selene\Module\Routing\Route');
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
            '\Selene\Module\Routing\RouteCollection',
            unserialize(serialize($this->collection))
        );
    }
}
