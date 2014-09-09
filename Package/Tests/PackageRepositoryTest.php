<?php

/**
 * This File is part of the Selene\Module\Package\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Package\Tests;

use \Mockery as m;
use \Selene\Module\DI\Container;
use \Selene\Module\DI\Parameters;
use \Selene\Module\Package\PackageInterface;
use \Selene\Module\Package\PackageRepository;
use \Selene\Module\TestSuite\TestCase;

class PackageRepositoryTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $repo = new PackageRepository;
        $this->assertInstanceof('\Selene\Module\Package\PackageRepository', $repo);
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
        $builder = m::mock('\Selene\Module\DI\BuilderInterface');
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
    public function itShouldBeLoadedBeforeBuild()
    {
        $called = false;

        $repo = new PackageRepository([], $cfl = $this->mockConfigLoader());
        $cfl->shouldIgnoreMissing()
            ->shouldReceive('load')->once()->andReturnUsing(function () use (&$called) {
                $called = true;
            });

        $p = $this->mockPackage();
        $p->shouldReceive('getAlias')->andReturn('foo');
        $p->shouldIgnoreMissing();

        $repo->add($p);
        $repo->build($this->mockBuilder());

        $this->assertTrue($called);
    }

    /** @test */
    public function itShouldUnloadCongAfterBuild()
    {
        $called = false;
        $repo = new PackageRepository([], $cfl = $this->mockConfigLoader());
        $cfl->shouldIgnoreMissing()
            ->shouldReceive('unload')->once()->andReturnUsing(function () use (&$called) {
                $called = true;
            });

        $repo->build($this->mockBuilder());

        $this->assertTrue($called);
    }

    protected function mockBuilder()
    {
        return m::mock('\Selene\Module\DI\BuilderInterface');
    }

    protected function mockConfigLoader()
    {
        return m::mock('Selene\Module\Package\ConfigLoader');
    }

    /** @test */
    public function itShouldLoadPackageConfig()
    {
        $pass = false;

        $config =  $this->getConfigInterface();


        $params    = m::mock('\Selene\Module\DI\ParameterInterface');
        $params->shouldReceive('getRaw')->andReturn([]);
        $params->shouldReceive('all')->andReturn([]);
        $params->shouldReceive('merge');

        $container = new Container($params);

        $builder = m::mock('\Selene\Module\DI\BuilderInterface');
        $builder->shouldReceive('getContainer')->andReturn($container);
        $builder->shouldReceive('addObjectResource');
        $builder->shouldReceive('addFileResource');
        $builder->shouldReceive('replaceContainer')->andReturnUsing(function ($container) {
            $this->assertInstanceof('\Selene\Module\DI\ContainerInterface', $container);
        });

        $builder->shouldReceive('getPackageConfig')->andReturn([]);

        $config->shouldReceive('load')->andReturnUsing(function ($cbuilder) use ($builder) {
            $this->assertInstanceof('\Selene\Module\DI\BuilderInterface', $cbuilder);
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
        //$this->markTestIncomplete('feature Probably won\'t make it.');

        $p10 = m::mock('Selene\Module\Package\PackageInterface');
        $p10->shouldReceive('getName')->andReturn('p10Package')
           ->shouldReceive('getAlias')->andReturn('p10')
           ->shouldReceive('requires')->andReturn([]);

        $p1 = m::mock('Selene\Module\Package\PackageInterface');
        $p1->shouldReceive('getName')->andReturn('p1Package')
           ->shouldReceive('getAlias')->andReturn('p1')
           ->shouldReceive('requires')->andReturn(['p10']);

        $p2 = m::mock('Selene\Module\Package\PackageInterface');
        $p2->shouldReceive('getName')->andReturn('p2Package')
           ->shouldReceive('getAlias')->andReturn('p2')
           ->shouldReceive('requires')->andReturn(['p1', 'p4']);

        $p3 = m::mock('Selene\Module\Package\PackageInterface');
        $p3->shouldReceive('getName')->andReturn('p3Package')
           ->shouldReceive('getAlias')->andReturn('p3')
           ->shouldReceive('requires')->andReturn(['p1']);

        $p4 = m::mock('Selene\Module\Package\PackageInterface');
        $p4->shouldReceive('getName')->andReturn('p4Package')
           ->shouldReceive('getAlias')->andReturn('p4')
           ->shouldReceive('requires')->andReturn(['p1', 'p5']);

        $p5 = m::mock('Selene\Module\Package\PackageInterface');
        $p5->shouldReceive('getName')->andReturn('p5Package')
           ->shouldReceive('getAlias')->andReturn('p5')
           ->shouldReceive('requires')->andReturn(['p1', 'p3']);

        $repo = new PackageRepository([
            $p1, $p2, $p3, $p4, $p5, $p10
        ]);

        $this->assertEquals(
            ['p10', 'p1', 'p3', 'p5', 'p4', 'p2'],
            array_keys($this->invokeObjectMethod('getSorted', $repo))
        );
    }

    protected function mockPackage($requirement = [])
    {
        $package = m::mock('\Selene\Module\Package\PackageInterface');
        $package->shouldReceive('requires')->andReturn($requirement);

        return $package;
    }

    public function getConfigInterface()
    {
        $config = m::mock('\Selene\Module\Config\ConfigurationInterface');
        return $config;
    }
}
