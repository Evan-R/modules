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

    /** @test */
    public function itShouldParseAnyRoutes()
    {
        $fname = 'routes.2.xml';
        $loader = $this->newLoader();

        $this->prepareLoader($fname);

        $loader->load($fname);

        $routes = $this->container->get('routes');
        $this->assertTrue($routes->has('any'));

        $anyMethods = [
            'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'
            ];

        sort($anyMethods);
        $methods =  $routes->get('any')->getMethods();
        sort($methods);

        $this->assertSame($anyMethods, $methods);
    }

    /** @test */
    public function itShouldParseGroups()
    {
        $fname = 'routes.3.xml';
        $loader = $this->newLoader();

        $this->prepareLoader($fname);

        $loader->load($fname);

        $routes = $this->container->get('routes');
        $this->assertTrue($routes->has('index.bar'));

        $this->assertSame('/foo/bar', $routes->get('index.bar')->getPattern());
    }

    /** @test */
    public function itShouldParseResource()
    {
        $fname = 'routes.4.xml';
        $loader = $this->newLoader();

        $this->prepareLoader($fname);

        $loader->load($fname);

        $routes = $this->container->get('routes');

        //var_dump($routes);
        $this->assertTrue($routes->has('user_photo.index'));
        $this->assertTrue($routes->has('user_photo.create'));

        $this->assertSame('/user/photo', $routes->get('user_photo.index')->getPattern());
        $this->assertSame('/user/photo/create', $routes->get('user_photo.create')->getPattern());

        $this->assertTrue($routes->has('user_tags.index'));
        $this->assertFalse($routes->has('user_tags.create'));
    }

    /** @test */
    public function itShouldAddParameterConstraints()
    {
        $fname = 'routes.5.xml';
        $loader = $this->newLoader();

        $this->prepareLoader($fname);

        $loader->load($fname);

        $routes = $this->container->get('routes');

        $this->assertTrue($routes->has('user'));

        $this->assertSame('(\d+)', $routes->get('user')->getConstraint('id'));

        $this->assertTrue($routes->has('admin'));

        $this->assertSame('(dev|com)', $routes->get('admin')->getHostConstraint('tld'));
        $this->assertSame('dev', $routes->get('admin')->getHostDefault('tld'));

        $this->assertTrue($routes->has('members'));

        $this->assertSame(12, $routes->get('members')->getDefault('id'));
    }

    /** @test */
    public function itShouldThrowExceptionIfActionIsMissing()
    {
        $fname = 'routes.err.0.xml';
        $loader = $this->newLoader();

        $this->prepareLoader($fname);

        $this->builder->shouldReceive('addFileResource');

        try {
            $loader->load($fname);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Route requires an action.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldThrowExceptionIfPathIsMissing()
    {
        $fname = 'routes.err.1.xml';
        $loader = $this->newLoader();

        $this->prepareLoader($fname);

        $this->builder->shouldReceive('addFileResource');

        try {
            $loader->load($fname);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Route requires a path.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldThrowExceptionIfNameIsMissing()
    {
        $fname = 'routes.err.2.xml';
        $loader = $this->newLoader();

        $this->prepareLoader($fname);

        $this->builder->shouldReceive('addFileResource');

        try {
            $loader->load($fname);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Route requires a name.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldThrowExceptionIfMethodIsMissing()
    {
        $fname = 'routes.err.3.xml';
        $loader = $this->newLoader();

        $this->prepareLoader($fname);

        $this->builder->shouldReceive('addFileResource');

        try {
            $loader->load($fname);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Route requires at least one method.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldThrowExceptionIfGroupPrefixIsMissing()
    {
        $fname = 'routes.err.4.xml';
        $loader = $this->newLoader();

        $this->prepareLoader($fname);

        $this->builder->shouldReceive('addFileResource');

        try {
            $loader->load($fname);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('A routing group requires a prefix.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldThrowExceptionIfResourceControllerIsMissing()
    {
        $fname = 'routes.err.5.xml';
        $loader = $this->newLoader();

        $this->prepareLoader($fname);

        $this->builder->shouldReceive('addFileResource');

        try {
            $loader->load($fname);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Resource requires a controller.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldThrowExceptionIfResourcePathIsMissing()
    {
        $fname = 'routes.err.6.xml';
        $loader = $this->newLoader();

        $this->prepareLoader($fname);

        $this->builder->shouldReceive('addFileResource');

        try {
            $loader->load($fname);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Resource requires a path.', $e->getMessage());
        }
    }

    protected function setUp()
    {
        $this->builder = m::mock('Selene\Components\DI\BuilderInterface');
        $this->locator = m::mock('Selene\Components\Config\Resource\LocatorInterface');

        $this->builder->shouldReceive('getContainer')->andReturn($this->container = new Container);
    }

    protected function prepareLoader($fname)
    {
        $this->locator->shouldReceive('locate')
            ->with($fname, true)
            ->andReturn([$file = __DIR__ . '/Fixures/'.$fname]);

        $this->builder->shouldReceive('addFileResource');
    }

    protected function newLoader($builder = null, $locator = null)
    {
        return new XmlLoader($builder ?: $this->builder, $locator ?: $this->locator, 'routes');
    }
}
