<?php

/**
 * This File is part of the Selene\Components\Package\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package\Tests;

use \Mockery as m;
use \Selene\Components\DI\Parameters;
use \Selene\Components\Package\PackageInterface;
use \Selene\Components\Package\PackageRepository;

class PackageRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $repo = new PackageRepository;
        $this->assertInstanceof('\Selene\Components\Package\PackageRepository', $repo);
    }

    /** @test */
    public function itShouldadds()
    {
        $package = m::mock('\Selene\Components\Package\PackageInterface');

        $package->shouldReceive('getName')->andReturn('AcmePackage');
        $package->shouldReceive('getAlias')->andReturn('acme');

        $repo = new PackageRepository;
        $repo->add($package);

        $this->assertTrue($repo->has('acme'));
    }

    /** @test */
    public function itShouldGetAPackageByName()
    {
        $package = m::mock('\Selene\Components\Package\PackageInterface');

        $package->shouldReceive('getName')->andReturn('AcmePackage');
        $package->shouldReceive('getAlias')->andReturn('acme');

        $repo = new PackageRepository;
        $repo->add($package);

        $this->assertSame($package, $repo->get('AcmePackage'));
    }

    /** @test */
    public function itShouldGetAPackageByAlias()
    {
        $package = m::mock('\Selene\Components\Package\PackageInterface');

        $package->shouldReceive('getName')->andReturn('AcmePackage');
        $package->shouldReceive('getAlias')->andReturn('acme');

        $repo = new PackageRepository;
        $repo->add($package);

        $this->assertSame($package, $repo->get('acme'));
    }

    /** @test */
    public function itShouldGetAllPackages()
    {
        $package = m::mock('\Selene\Components\Package\PackageInterface');

        $package->shouldReceive('getName')->andReturn('AcmePackage');
        $package->shouldReceive('getAlias')->andReturn('acme');

        $repo = new PackageRepository;
        $repo->add($package);

        $this->assertEquals(['acme' => $package], $repo->all());
    }

    /** @test */
    public function itShouldCallBuildOnPackages()
    {
        $pass = false;
        $container = m::mock('\Selene\Components\DI\ContainerInterface');
        $container->shouldReceive('addFileResource')->with('meta.xml');

        $package = m::mock('\Selene\Components\Package\PackageInterface');

        $package->shouldReceive('getMeta')->andReturn('meta.xml');
        $package->shouldReceive('getName')->andReturn('AcmePackage');
        $package->shouldReceive('getAlias')->andReturn('acme');
        $package->shouldReceive('getConfiguration')->andReturn(null);
        $package->shouldReceive('build')->with($container)->andReturnUsing(function () use (&$pass) {
            $pass = true;
            $this->assertTrue(true);
        });

        $repo = new PackageRepository;
        $repo->add($package);

        $repo->build($container);

        if (!$pass) {
            $this->fail();
        }
    }

    /** @test */
    public function itShouldLoadPackageConfig()
    {
        $pass = false;

        $config =  $this->getConfigInterface();

        $params    = m::mock('\Selene\Components\DI\ParameterInterface');
        $params->shouldReceive('getRaw')->andReturn([]);
        $params->shouldReceive('merge');

        $container = new \Selene\Components\DI\BaseContainer($params);
        $config->shouldReceive('load')->andReturnUsing(function ($packageContainer) use ($container) {
            $this->assertInstanceof('\Selene\Components\DI\ContainerInterface', $packageContainer);
            $this->assertTrue($container !== $packageContainer);
        });

        $package = m::mock('\Selene\Components\Package\PackageInterface');

        $package->shouldReceive('getMeta')->andReturn('meta.xml');
        $package->shouldReceive('getName')->andReturn('AcmePackage');
        $package->shouldReceive('getAlias')->andReturn('acme');
        $package->shouldReceive('getConfiguration')->andReturn($config);
        $package->shouldReceive('build')->with($container)->andReturnUsing(function () use (&$pass) {
            $pass = true;
            $this->assertTrue(true);
        });

        $repo = new PackageRepository;
        $repo->add($package);

        $repo->build($container);

        if (!$pass) {
            $this->fail();
        }
    }

    public function getConfigInterface()
    {
        $config = m::mock('\Selene\Components\Config\ConfigurationInterface');
        return $config;
    }
}
