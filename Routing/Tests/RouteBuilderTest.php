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

use \Selene\Module\Routing\Route;
use \Selene\Module\Routing\RouteBuilder;

class RouteBuilderTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstantiable()
    {
        $builder = new RouteBuilder;
        $this->assertInstanceof('\Selene\Module\Routing\RouteBuilder', $builder);
    }

    /** @test */
    public function itShouldCreateRoutes()
    {
        $builder = new RouteBuilder;

        $route = $builder->define('GET', 'foo', '/foo', ['_action' => 'foo']);

        $this->assertInstanceof('\Selene\Module\Routing\Route', $route);
    }

    /** @test */
    public function itShouldBeAbleToGroupRoutes()
    {
        $builder = new RouteBuilder;

        $builder->group(
            '/',
            ['_host' => 'localhost', '_before' => 'auth'],
            function ($builder) {
                $builder->define('GET', 'foo', '/foo', ['_action' => 'foo']);
                $builder->group('/bar', function ($builder) {
                    $builder->define('GET', 'foo.foo', '/foo', ['_action' => 'bar']);
                });
            }
        );

    }

    /** @test */
    public function itShouldCreateResources()
    {
        $builder = new RouteBuilder;

        $builder->resource('/user/photos', 'PhotoController');

        $routes = $builder->getRoutes();

        $this->assertTrue($routes->has('user_photos.index'));
        $this->assertEquals('/user/photos', $routes->get('user_photos.index')->getPattern());
        $this->assertEquals(['GET', 'HEAD'], $routes->get('user_photos.index')->getMethods());

        $this->assertTrue($routes->has('user_photos.create'));
        $this->assertEquals('/user/photos/create', $routes->get('user_photos.create')->getPattern());
        $this->assertEquals(['GET', 'HEAD'], $routes->get('user_photos.create')->getMethods());

        $this->assertTrue($routes->has('user_photos.new'));
        $this->assertEquals('/user/photos', $routes->get('user_photos.new')->getPattern());
        $this->assertEquals(['POST'], $routes->get('user_photos.new')->getMethods());

        $this->assertTrue($routes->has('user_photos.show'));
        $this->assertEquals('/user/photos/{resource}', $routes->get('user_photos.show')->getPattern());
        $this->assertEquals(['GET', 'HEAD'], $routes->get('user_photos.show')->getMethods());

        $this->assertTrue($routes->has('user_photos.edit'));
        $this->assertEquals('/user/photos/edit/{resource}', $routes->get('user_photos.edit')->getPattern());
        $this->assertEquals(['GET', 'HEAD'], $routes->get('user_photos.edit')->getMethods());

        $this->assertTrue($routes->has('user_photos.update'));
        $this->assertEquals('/user/photos/{resource}', $routes->get('user_photos.update')->getPattern());
        $this->assertEquals(['PUT', 'PATCH'], $routes->get('user_photos.update')->getMethods());

        $this->assertTrue($routes->has('user_photos.delete'));
        $this->assertEquals('/user/photos/{resource}', $routes->get('user_photos.delete')->getPattern());
        $this->assertEquals(['DELETE'], $routes->get('user_photos.delete')->getMethods());
    }

    /** @test */
    public function itShouldOnlyCreateRoutesForGivenActions()
    {
        $builder = new RouteBuilder;

        $builder->resource('/user/photos', 'PhotoController', ['index', 'new']);
        $routes = $builder->getRoutes();

        $this->assertTrue($routes->has('user_photos.index'));
        $this->assertTrue($routes->has('user_photos.new'));

        $this->assertFalse($routes->has('user_photos.create'));
        $this->assertFalse($routes->has('user_photos.show'));
        $this->assertFalse($routes->has('user_photos.edit'));
        $this->assertFalse($routes->has('user_photos.delete'));
    }

    /** @test */
    public function itShouldAddConstraitToTheResourceVariable()
    {
        $builder = new RouteBuilder;

        $builder->resource('/user/photos', 'PhotoController', [], $regexp = '(\d+)');
        $routes = $builder->getRoutes();

        $this->assertSame($regexp, $routes->get('user_photos.edit')->getConstraint('resource'));
        $this->assertSame($regexp, $routes->get('user_photos.show')->getConstraint('resource'));
        $this->assertSame($regexp, $routes->get('user_photos.delete')->getConstraint('resource'));
    }
}
