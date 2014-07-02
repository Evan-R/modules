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
use \Selene\Components\DI\Container;
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
        $builder = m::mock('\Selene\Components\DI\BuilderInterface');
        $builder->shouldReceive('addFileResource')->with('package.xml');

        $container = new Container;
        $builder->shouldReceive('getContainer')->andReturn($container);

        $package = m::mock('\Selene\Components\Package\PackageInterface');

        $package->shouldReceive('getMeta')->andReturn('package.xml');
        $package->shouldReceive('getName')->andReturn('AcmePackage');
        $package->shouldReceive('getAlias')->andReturn('acme');
        $package->shouldReceive('getConfiguration')->andReturn(null);
        $package->shouldReceive('build')->with($builder)->andReturnUsing(function () use (&$pass) {
            $pass = true;
            $this->assertTrue(true);
        });

        $repo = new PackageRepository;
        $repo->add($package);

        $repo->build($builder);

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

        $container = new Container($params);

        $builder = m::mock('\Selene\Components\DI\BuilderInterface');
        $builder->shouldReceive('getContainer')->andReturn($container);
        $builder->shouldReceive('addObjectResource');
        $builder->shouldReceive('addFileResource');
        $builder->shouldReceive('replaceContainer')->andReturnUsing(function ($container) {
            $this->assertInstanceof('\Selene\Components\DI\ContainerInterface', $container);
        });

        $builder->shouldReceive('getPackageConfig')->andReturn([]);

        $config->shouldReceive('load')->andReturnUsing(function ($cbuilder) use ($builder) {
            $this->assertInstanceof('\Selene\Components\DI\BuilderInterface', $cbuilder);
            $this->assertSame($builder, $cbuilder);
        });

        $package = m::mock('\Selene\Components\Package\PackageInterface');

        $package->shouldReceive('getMeta')->andReturn('package.xml');
        $package->shouldReceive('getName')->andReturn('AcmePackage');
        $package->shouldReceive('getAlias')->andReturn('acme');
        $package->shouldReceive('getConfiguration')->andReturn($config);
        $package->shouldReceive('build')->with($builder)->andReturnUsing(function () use (&$pass) {
            $pass = true;
            $this->assertTrue(true);
        });

        $repo = new PackageRepository;
        $repo->add($package);

        $repo->build($builder);

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
