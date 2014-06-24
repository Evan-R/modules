<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests\Loader;

use \Mockery as m;
use \Selene\Components\DI\Container;
use \Selene\Components\Routing\Loader\XmlLoader;

/**
 * @class XmlLoaderTest
 * @package Selene\Components\Routing
 * @version $Id$
 */
class XmlLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            '\Selene\Components\Routing\Loader\XmlLoader',
            new XmlLoader(
                $this->builder,
                $this->locator
            )
        );
    }

    /** @test */
    public function itShouldLoadXmlConfigs()
    {
        $loaded = false;
        $loader = $this->newLoader();
        $fname = 'routes.0.xml';

        $this->locator->shouldReceive('locate')
            ->with($fname, true)
            ->andReturn([$file = __DIR__ . '/Fixures/routes.0.xml']);

        $this->builder->shouldReceive('addFileResource')
            ->once()
            ->with($file)
            ->andReturnUsing(function ($f) use ($file, &$loaded) {
                $loaded = true;
                $this->assertSame($file, $f);
            });

        $loader->load($fname);

        $this->assertTrue($loaded);

        $this->assertTrue($this->container->get('routes')->has('index'));

        //$this->assertTrue($routes->has('index'));
        //$this->assertTrue($routes->has('index.create'));
        //$this->assertTrue($routes->has('index.edit'));
        //$this->assertTrue($routes->has('index.delete'));

        //$this->assertTrue($routes->has('baz'));
        //$this->assertTrue($routes->has('baz.show'));

        //$this->assertTrue($routes->has('foo'));
        //$this->assertTrue($routes->has('foo.bar'));
    }

    /** @test */
    public function itShouldLoadXmlFilesAndImports()
    {
        $loadedA = false;
        $loadedB = false;
        $loader = $this->newLoader();
        $fnameA = 'routes.1.xml';
        $fnameB = 'routes.0.xml';

        $this->locator->shouldReceive('locate')
            ->with($fnameA, true)
            ->andReturn([$fileA = __DIR__ . '/Fixures/routes.1.xml']);

        $this->locator->shouldReceive('locate')
            ->with($fnameB, true)
            ->andReturn([$fileB = __DIR__ . '/Fixures/routes.0.xml']);

        $this->builder->shouldReceive('addFileResource')
            ->once()
            ->with($fileA)
            ->andReturnUsing(function ($f) use ($fileA, &$loadedA) {
                $loadedA = true;
                $this->assertSame($fileA, $f);
            });

        $this->builder->shouldReceive('addFileResource')
            ->once()
            ->with($fileB)
            ->andReturnUsing(function ($f) use ($fileB, &$loadedB) {
                $loadedB = true;
                $this->assertSame($fileB, $f);
            });


        $loader->load($fnameA);

        $this->assertTrue($this->container->get('routes')->has('index'));
    }

    protected function setUp()
    {
        $this->builder = m::mock('Selene\Components\DI\BuilderInterface');
        $this->locator = m::mock('Selene\Components\Config\Resource\LocatorInterface');

        $this->builder->shouldReceive('getContainer')->andReturn($this->container = new Container);
    }

    protected function newLoader($builder = null, $locator = null)
    {
        return new XmlLoader($builder ?: $this->builder, $locator ?: $this->locator, 'routes');
    }
}
