<?php

/**
 * This File is part of the Selene\Module\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests;

use \Mockery as m;
use \Selene\Module\DI\Builder;
use \Selene\Module\DI\Container;
use \Selene\Module\DI\Tests\Stubs\BuilderStub;
use \Selene\Module\DI\ContainerInterface;
use \Selene\Module\DI\Processor\ProcessInterface;
use \Selene\Module\DI\Processor\ProcessorInterface;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Selene\Module\DI\Builder',
            new Builder(
                $this->getContainerMock()
            )
        );
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

        $this->assertInstanceof('\Selene\Module\DI\Processor\ProcessorInterface', $builder->getProcessor());
    }

    /** @test */
    public function processorShouldBeSettable()
    {
        $builder = new Builder(
            $this->getContainerMock(),
            $processor = m::mock('\Selene\Module\DI\Processor\ProcessorInterface')
        );

        $this->assertInstanceof('\Selene\Module\DI\Processor\ProcessorDecorator', $builder->getProcessor());
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

        $container->shouldReceive('getAliases')->andReturn([]);
        $this->prepareContainerToBuild($container);

        $this->assertNull($builder->build());
    }

    /** @test */
    public function itPassMethodCallsToItsContainer()
    {
        $builder = new Builder($container = $this->getContainerMock());

        $container->shouldReceive('getDefinitions')->andReturn([]);

        $this->assertSame([], $builder->getDefinitions());
    }

    /** @test */
    public function itPassCheckIfPassedMethodExists()
    {
        $builder = new Builder($container = $this->getContainerMock());

        $container->shouldReceive('getDefinitions')->andReturn([]);

        try {
            $builder->getStuff();
        } catch (\BadMethodCallException $e) {
            $this->assertTrue(false !== stripos($e->getMessage(), 'getStuff'));
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->fail('test splipped');
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
            $params = m::mock('\Selene\Module\DI\ParameterInterface')
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
        return m::mock('\Selene\Module\DI\ContainerInterface');
    }

    protected function getProcessorConfigMock()
    {
        return m::mock('\Selene\Module\DI\Processor\ConfigInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
