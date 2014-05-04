<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Loader;


use \Mockery as m;
use \Selene\Components\DI\Builder;
use \Selene\Components\DI\Container;
use \Selene\Components\DI\Loader\XmlLoader;
use \Selene\Components\Config\Resource\Locator;

/**
 * @class XmlLoaderTest
 * @package Selene\Components\DI\Tests\Loader
 * @version $Id$
 */
class XmlLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable()
    {
        $container = m::mock('\Selene\Components\DI\ContainerInterface');
        $builder = m::mock('\Selene\Components\DI\BuilderInterface');
        $locator   = m::mock('\Selene\Components\Config\Resource\LocatorInterface');

        $builder->shouldReceive('getContainer')->andReturn($container);

        $loader = new XmlLoader($builder, $locator);
        $this->assertInstanceof('\Selene\Components\DI\Loader\XmlLoader', $loader);
    }

    /** @test */
    public function itShouldSupportXml()
    {
        $loader = $this->getLoaderMock();

        $this->assertTrue($loader->supports('some/file.xml'));
        $this->assertFalse($loader->supports('some/file.php'));
    }

    /** @test */
    public function itShouldParseParameters()
    {
        $loader = new XmlLoader(new Builder($container = new Container), new Locator([__DIR__.'/Fixures']));

        $loader->load('services.0.xml');

        $this->assertTrue($container->hasParameter('foo'));
        $this->assertSame('bar', $container->getParameter('foo'));
    }

    /** @test */
    public function itShouldParseServices()
    {

        $loader = new XmlLoader(new Builder($container = new Container), new Locator([__DIR__.'/Fixures']));

        $loader->load('services.1.xml');

        $this->assertTrue($container->hasDefinition('foo_service'));
        $this->assertSame('StdClass', $container->getDefinition('foo_service')->getClass());
    }

    /** @test */
    public function itShouldParseImportedFiles()
    {

        $loader = new XmlLoader($builder = new Builder($container = new Container), new Locator([__DIR__.'/Fixures']));

        $loader->load('services.2.xml');

        $this->assertSame([['foo' => 'bar']], $builder->getExtensionConfig('acme'));
    }

    /** @test */
    public function itShouldAddResourcesToTheBuilder()
    {
        $loader = new XmlLoader($builder = new Builder($container = new Container), new Locator([$dir = __DIR__.'/Fixures']));

        $loader->load('services.2.xml');

        $resources = $builder->getResources();

        $this->assertSame(2, count($resources));

        $this->assertSame($dir.DIRECTORY_SEPARATOR.'services.2.xml', (string)$resources[0]);
        $this->assertSame($dir.DIRECTORY_SEPARATOR.'imported.0.xml', (string)$resources[1]);
    }

    protected function getLoaderMock($builder = null, $container = null, $locator = null)
    {
        $builder =   $builder = $builder ?: m::mock('\Selene\Components\DI\BuilderInterface');
        $container = m::mock('\Selene\Components\DI\ContainerInterface');
        $locator   = $locator ?: m::mock('\Selene\Components\Config\Resource\LocatorInterface');

        $builder->shouldReceive('getContainer')->andReturn($container);

        return  new XmlLoader($builder, $locator);
    }
}
