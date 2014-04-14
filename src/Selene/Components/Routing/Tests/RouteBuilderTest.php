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

use \Selene\Components\Routing\Route;
use \Selene\Components\Routing\RouteBuilder;

class RouteBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassName
     */
    protected $subject;

    protected function setUp()
    {
        $this->builder = new RouteBuilder;
    }

    /**
     * @test
     */
    public function itShouldAddANewRouteToTheCollection()
    {
        $this->builder->make('get', 'app', '/');
        $this->builder->make('put', 'app.update', '/');

        $this->assertEquals(['GET', 'HEAD'], $this->builder->getRoutes()->get('app')->getMethods());
        $this->assertEquals(['PUT', 'PATCH'], $this->builder->getRoutes()->get('app.update')->getMethods());

        $this->assertEquals('app', $this->builder->getRoutes()->get('app')->getName());
        $this->assertEquals('app.update', $this->builder->getRoutes()->get('app.update')->getName());
    }

    /**
     * @test
     */
    public function itShouldNestGroupsCorrectly()
    {
        $this->builder->group('app', '/', function ($builder) {
            $builder->make('GET', 'index', '/', ['_action' => 'view.index:showIndex']);
            $builder->make('GET', 'foo', '/foo', ['_action' => 'view.index:showFoo']);

            $builder->group('sub', '/sub', function ($builder) {
                $builder->make('GET', 'bar', '/bar', ['_action' => 'view.sub:showBar']);
                $builder->group('deep', '/deep', function ($builder) {
                    $builder->make('GET', 'bazoo', '/faaaz', ['_action' => 'view.deep:showBazoo']);
                });
            });
        });

        $routes = $this->builder->getRoutes();

        $this->assertTrue($routes->has('app.index'));

        $this->assertTrue($routes->has('app.foo'));

        $this->assertTrue($routes->has('app.sub.bar'));

        $this->assertTrue($routes->has('app.sub.deep.bazoo'));
    }

    /**
     * @test
     */
    public function routesShouldBeInsertable()
    {
        $this->builder->make('GET', 'index', '/app');

        $this->builder->insert('index', 'GET', 'user', '/user');

        $routes = $this->builder->getRoutes();

        $this->assertTrue($routes->has('index.user'));
        $route = $routes->get('index.user');

        $this->assertEquals('/app/user', $route->getPath());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowExceptionWhenInsertingToUnknownParent()
    {
        $this->builder->insert('index', 'GET', 'user', '/user');
    }

    /**
     * @test
     */
    public function testShortCutMethods()
    {
        $this->builder->routeAny('any', '/');
        $this->builder->routeGet('index', '/app');
        $this->builder->routePost('index.create', '/app/create');
        $this->builder->routePut('index.update', '/app/update');
        $this->builder->routeDelete('index.delete', '/ap/delete');

        $routes = $this->builder->getRoutes();

        $this->assertTrue($routes->has('any'));
        $this->assertTrue($routes->has('index'));
        $this->assertTrue($routes->has('index.create'));
        $this->assertTrue($routes->has('index.update'));
        $this->assertTrue($routes->has('index.delete'));

        $this->assertTrue(in_array('GET', $routes->get('any')->getMethods()));
        $this->assertTrue(in_array('POST', $routes->get('any')->getMethods()));
        $this->assertTrue(in_array('PUT', $routes->get('any')->getMethods()));
        $this->assertTrue(in_array('DELETE', $routes->get('any')->getMethods()));

        $this->assertTrue(in_array('GET', $routes->get('index')->getMethods()));
        $this->assertTrue(in_array('POST', $routes->get('index.create')->getMethods()));
        $this->assertTrue(in_array('PUT', $routes->get('index.update')->getMethods()));
        $this->assertTrue(in_array('DELETE', $routes->get('index.delete')->getMethods()));
    }
}
