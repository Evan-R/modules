<?php

/**
 * This File is part of the Selene\Module\DI\Tests\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests\Loader;

use \Mockery as m;
use \Selene\Module\DI\Loader\PhpLoader;
use \Selene\Module\DI\Builder;
use \Selene\Module\DI\Container;
use \Selene\Module\Config\Resource\Locator;

/**
 * @class PhpLoaderInterface
 * @package Selene\Module\DI\Tests\Loader
 * @version $Id$
 */
class PhpLoaderTest extends \PHPUnit_Framework_TestCase
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
        $loader = $this->getLoaderMock();

        $this->assertInstanceof('\Selene\Module\DI\Loader\PhpLoader', $loader);
    }

    /** @test */
    public function itShouldSupportPhpFiles()
    {
        $container = m::mock('\Selene\Module\DI\ContainerInterface');
        $loader = $this->getLoaderMock();

        $this->assertTrue($loader->supports('some/file.php'));
        $this->assertFalse($loader->supports('some/file.xml'));
    }

    /** @test */
    public function itShouldLoadPhpFilesAndExposeBuilderVars()
    {
        $loader = new PhpLoader(new Builder($container = new Container), new Locator([__DIR__.'/Fixures']));

        $loader->load('services.php');

        $this->assertTrue($container->hasParameter('foo'));
        $this->assertTrue($container->hasDefinition('foo_service'));
    }

    /** @test */
    public function itShouldAddFileResources()
    {
        $loader = new PhpLoader($builder = new Builder($container = new Container), new Locator([$dir = __DIR__.'/Fixures']));

        $loader->load('services.php');

        $resources = $builder->getResources();

        $this->assertSame(1, count($resources));
        $this->assertSame($dir.DIRECTORY_SEPARATOR.'services.php', (string)$resources[0]);
    }

    protected function getLoaderMock($builder = null, $container = null, $locator = null)
    {
        $builder =   $builder = $builder ?: m::mock('\Selene\Module\DI\BuilderInterface');
        $container = m::mock('\Selene\Module\DI\ContainerInterface');
        $locator   = $locator ?: m::mock('\Selene\Module\Config\Resource\LocatorInterface');

        $builder->shouldReceive('getContainer')->andReturn($container);

        return  new PhpLoader($builder, $locator);
    }
}
