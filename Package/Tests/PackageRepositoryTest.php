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
        $package = $this->mockPackage();

        $package->shouldReceive('getName')->andReturn('AcmePackage');
        $package->shouldReceive('getAlias')->andReturn('acme');

        $repo = new PackageRepository;
        $repo->add($package);

        $this->assertTrue($repo->has('acme'));
    }

    /** @test */
    public function itShouldGetAPackageByName()
    {
        $package = $this->mockPackage();

        $package->shouldReceive('getName')->andReturn('AcmePackage');
        $package->shouldReceive('getAlias')->andReturn('acme');

        $repo = new PackageRepository;
        $repo->add($package);

        $this->assertSame($package, $repo->get('AcmePackage'));
    }

    /** @test */
    public function itShouldGetAPackageByAlias()
    {
        $package = $this->mockPackage();

        $package->shouldReceive('getName')->andReturn('AcmePackage');
        $package->shouldReceive('getAlias')->andReturn('acme');

        $repo = new PackageRepository;
        $repo->add($package);

        $this->assertSame($package, $repo->get('acme'));
    }

    /** @test */
    public function itShouldGetAllPackages()
    {
        $repo = new PackageRepository;

        $package = $this->mockPackage();

        $package->shouldReceive('getName')->andReturn('AcmePackage');
        $package->shouldReceive('getAlias')->andReturn('acme');

        $repo->add($package);

        $this->assertEquals(['acme' => $package], $repo->all());

        $packageB = $this->mockPackage();

        $packageB->shouldReceive('getName')->andReturn('FooPackage');
        $packageB->shouldReceive('getAlias')->andReturn('foo');

        $repo->add($packageB);

        $this->assertEquals(['acme' => $package, 'foo' => $packageB], $repo->all());
    }

    /** @test */
    public function itShouldCallBuildOnPackages()
    {
        $pass = false;
        $builder = m::mock('\Selene\Components\DI\BuilderInterface');
        $builder->shouldReceive('addFileResource')->with('package.xml');

        $container = new Container;
        $builder->shouldReceive('getContainer')->andReturn($container);

        $package = $this->mockPackage();

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

        $package = $this->mockPackage();

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

    /** @test */
    public function itShouldGetPackagesInOrderOfTheirDependencies()
    {
        $p10 = m::mock('Selene\Components\Package\PackageInterface');
        $p10->shouldReceive('getName')->andReturn('p10Package')
           ->shouldReceive('getAlias')->andReturn('p10')
           ->shouldReceive('requires')->andReturn([]);

        $p1 = m::mock('Selene\Components\Package\PackageInterface');
        $p1->shouldReceive('getName')->andReturn('p1Package')
           ->shouldReceive('getAlias')->andReturn('p1')
           ->shouldReceive('requires')->andReturn(['p10']);

        $p2 = m::mock('Selene\Components\Package\PackageInterface');
        $p2->shouldReceive('getName')->andReturn('p2Package')
           ->shouldReceive('getAlias')->andReturn('p2')
           ->shouldReceive('requires')->andReturn(['p1', 'p4']);

        $p3 = m::mock('Selene\Components\Package\PackageInterface');
        $p3->shouldReceive('getName')->andReturn('p3Package')
           ->shouldReceive('getAlias')->andReturn('p3')
           ->shouldReceive('requires')->andReturn(['p1']);

        $p4 = m::mock('Selene\Components\Package\PackageInterface');
        $p4->shouldReceive('getName')->andReturn('p4Package')
           ->shouldReceive('getAlias')->andReturn('p4')
           ->shouldReceive('requires')->andReturn(['p1', 'p5']);

        $p5 = m::mock('Selene\Components\Package\PackageInterface');
        $p5->shouldReceive('getName')->andReturn('p5Package')
           ->shouldReceive('getAlias')->andReturn('p5')
           ->shouldReceive('requires')->andReturn(['p1', 'p3']);

        $repo = new PackageRepository([
            $p1, $p2, $p3, $p4, $p5, $p10
        ]);

        $this->assertEquals(['p10', 'p1', 'p3', 'p5', 'p4', 'p2'], array_keys($repo->all()));
    }

    protected function mockPackage($requirement = [])
    {
        $package = m::mock('\Selene\Components\Package\PackageInterface');
        $package->shouldReceive('requires')->andReturn($requirement);

        return $package;
    }

    public function getConfigInterface()
    {
        $config = m::mock('\Selene\Components\Config\ConfigurationInterface');
        return $config;
    }
}
