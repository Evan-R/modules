<?php

/**
 * This File is part of the Selene\Components\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests;

use \Mockery as m;
use \Selene\Components\DI\Builder;
use \Selene\Components\DI\Container;
use \Selene\Components\DI\Tests\Stubs\BuilderStub;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Processor\ProcessInterface;
use \Selene\Components\DI\Processor\ProcessorInterface;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Components\DI\Builder', new Builder($this->getContainerMock()));
    }

    /** @test */
    public function itShouldAddResources()
    {
        $builder = new Builder($this->getContainerMock());

        $builder->addObjectResource($this);
        $builder->addFileResource('somefile');

        $this->assertEquals([__FILE__, 'somefile'], $builder->getResources());
    }

    /** @test */
    public function itShouldAutoSetItsProcessor()
    {
        $builder = new Builder($this->getContainerMock());

        $this->assertInstanceof('\Selene\Components\DI\Processor\ProcessorInterface', $builder->getProcessor());
    }

    /** @test */
    public function processorShouldBeSettable()
    {
        $builder = new Builder(
            $this->getContainerMock(),
            $processor = m::mock('\Selene\Components\DI\Processor\ProcessorInterface')
        );

        $this->assertSame($processor, $builder->getProcessor());
    }

    /** @test */
    public function theContainerShouldBeReplaceable()
    {
        $builder = new Builder($cA = $this->getContainerMock());

        $this->assertSame($cA, $builder->getContainer());

        $builder->replaceContainer($cB = $this->getContainerMock());

        $this->assertSame($cB, $builder->getContainer());

        $this->assertFalse($cB === $cA);
    }

    /** @test */
    public function itShouldBeMergable()
    {
        $builderA = new Builder($cA = $this->getContainerMock());
        $builderB = new Builder($cB = $this->getContainerMock());

        $mergedCalled = false;

        $cA->shouldReceive('merge')->with($cB)->andReturnUsing(function () use (&$mergedCalled) {
            $mergedCalled = true;
        });

        $builderA->merge($builderB);

        $this->assertTrue($mergedCalled);
    }

    /** @test */
    public function itShouldBuildTheContainer()
    {
        $builder = new Builder($container = $this->getContainerMock());

        $this->prepareContainerToBuild($container);

        $this->assertNull($builder->build());
    }

    /**
     * prepareContainerToBuild
     *
     * @param mixed $container
     *
     * @access protected
     * @return void
     */
    protected function prepareContainerToBuild($container)
    {
        $container->shouldReceive('getParameters')->andReturn(
            $params = m::mock('\Selene\Components\DI\ParameterInterface')
        );

        $container->shouldReceive('getDefinitions')->andReturn([]);

        $params->shouldReceive('resolve')->andReturn($params);
        $params->shouldReceive('all')->andReturn([]);
    }

    /**
     * getContainerMock
     *
     * @access protected
     * @return ContainerInterface
     */
    protected function getContainerMock()
    {
        return m::mock('\Selene\Components\DI\ContainerInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
