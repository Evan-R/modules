<?php

/**
 * This File is part of the Selene\Components\Routing\Tests\DI\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests\Loader\DI;

use \Mockery as m;
use \Selene\Components\DI\Container;
use \Selene\Components\Routing\Loader\DI\XmlLoader;
use \Selene\Components\Routing\RouteCollection;
use \Selene\Components\Routing\Tests\Loader\LoaderTestHelper;

/**
 * @class XmlLoaderTest
 * @package Selene\Components\Routing\Tests\DI\Loader
 * @version $Id$
 */
class XmlLoaderTest extends \PHPUnit_Framework_TestCase
{
    use LoaderTestHelper;

    protected $container;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            '\Selene\Components\Routing\Loader\DI\XmlLoader',
            new XmlLoader(
                $this->builder,
                $this->routes,
                $this->locator
            )
        );
    }

    /** @test */
    public function itBuilderShouldBeNotifiedWhenAResourceWasLoaded()
    {
        $called = false;
        $fname = 'routes.0.xml';
        $loader = new XmlLoader($this->builder, $this->routes, $this->locator);

        $this->locator->shouldReceive('locate')
            ->with($fname, m::any())
            ->andReturn([$file = dirname(__DIR__) . '/Fixures/routes.0.xml']);

        $this->builder->shouldReceive('addFileResource')
            ->with($file)
            ->andReturnUsing(function ($resource) use (&$called, $file) {
                $this->assertSame($file, $resource);
                $called = true;
            });

        $loader->load($fname, true);
        $this->assertTrue($called);
    }

    /** @test */
    public function builderShouldBeNotifiedWheImportingResources()
    {
        $called = 0;
        $fnameA = 'routes.1.xml';
        $fnameB = 'routes.0.xml';
        $dir = dirname(__DIR__) . '/Fixures/';

        $files = [
            $dir.$fnameA,
            $dir.$fnameB,
        ];

        $loader = new XmlLoader($this->builder, $this->routes, $this->locator);

        $this->locator->shouldReceive('locate')
            ->with($fnameA, m::any())
            ->andReturn([$files[0]]);

        $this->locator->shouldReceive('locate')
            ->with($fnameB, m::any())
            ->andReturn([$files[1]]);

        $this->builder->shouldReceive('addFileResource')
            ->with(m::any())
            ->andReturnUsing(function ($resource) use (&$called, $files) {
                $this->assertTrue(in_array($resource, $files));
                $called++;
            });

        $loader->load($fnameA, true);
        $this->assertSame(2, $called);
    }

    protected function setUp()
    {
        $this->container = new Container;
        $this->routes    = new RouteCollection;

        $this->locator   = $this->mockLocator();
        $this->builder   = $this->mockBuilder();

        $this->builder->shouldReceive('getContainer')
            ->andReturn($this->container);
    }

    protected function tearDown()
    {
        m::close();
    }
}
