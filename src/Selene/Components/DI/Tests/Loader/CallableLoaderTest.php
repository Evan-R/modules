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
use \Selene\Components\Config\Resource\Locator;
use \Selene\Components\DI\Loader\CallableLoader;

/**
 * @class CallableLoaderTest
 * @package Selene\Components\DI\Tests\Loader
 * @version $Id$
 */
class CallableLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $loader = $this->getLoaderMock();

        $this->assertInstanceof('\Selene\Components\DI\Loader\CallableLoader', $loader);
    }

    /** @test */
    public function itShouldSupportCallables()
    {
        $container = m::mock('\Selene\Components\DI\ContainerInterface');
        $loader = $this->getLoaderMock();

        $this->assertTrue($loader->supports(function () {
        }));
        $this->assertTrue($loader->supports([$this, 'callableSetter']));
        $this->assertFalse($loader->supports('some/file.xml'));
    }

    /** @test */
    public function itShouldLoadCallablesAndExposeBuilderVars()
    {
        $loader = new CallableLoader(new Builder($container = new Container), new Locator([__DIR__.'/Fixures']));

        $loader->load(function ($builder) {
            $this->assertInstanceof('\Selene\Components\DI\BuilderInterface', $builder);
            $builder->getContainer()->setParameter('foo', 'bar');
            $builder->getContainer()->define('foo_service', 'StdClass');
        });

        $this->assertTrue($container->hasParameter('foo'));
        $this->assertTrue($container->hasDefinition('foo_service'));
    }

    /** @test */
    public function itShouldAddFileResources()
    {
        $loader = new CallableLoader($builder = new Builder($container = new Container), new Locator([__DIR__.'/Fixures']));

        $loader->load([$this, 'callableSetter']);
        $loader->load(function () {
        });

        $resources = $builder->getResources();

        $this->assertSame(2, count($resources));
        $this->assertSame(__FILE__, (string)$resources[0]);
        $this->assertSame(__FILE__, (string)$resources[1]);
    }

    public function callableSetter()
    {
    }

    protected function getLoaderMock($builder = null, $container = null, $locator = null)
    {
        $builder =   $builder = $builder ?: m::mock('\Selene\Components\DI\BuilderInterface');
        $container = m::mock('\Selene\Components\DI\ContainerInterface');
        $locator   = $locator ?: m::mock('\Selene\Components\Config\Resource\LocatorInterface');

        $builder->shouldReceive('getContainer')->andReturn($container);

        return  new CallableLoader($builder, $locator);
    }
}
