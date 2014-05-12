<?php

/**
 * This File is part of the Selene\Components\Stack\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Stack\Tests;

use \Mockery as m;
use \Selene\Components\Stack\StackBuilder;
use \Symfony\Component\HttpFoundation\Response;

class StackBuilderTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Components\Stack\StackBuilder', new StackBuilder($this->getKernelMock()));
    }

    /** @test */
    public function middlewaresShouldBeAddable()
    {
        $builder = new StackBuilder($kernel = $this->getKernelMock());

        $midA    =  $this->getStackedKernelMock();
        $midB    =  $this->getStackedKernelMock();

        $midA->shouldReceive('getPriority')->andReturn(10);
        $midB->shouldReceive('getPriority')->andReturn(0);

        $midA->shouldReceive('setKernel')->with($kernel);
        $midA->shouldReceive('getKernel')->andReturn($kernel);
        $midB->shouldReceive('setKernel')->with($midA);
        $midB->shouldReceive('getKernel')->andReturn($midA);

        $builder->add($midA);
        $builder->add($midB);

        $stack = $builder->make();

        $this->assertInstanceof('Selene\Components\Stack\Stack', $stack);
        $this->assertInstanceof('Symfony\Component\HttpKernel\HttpKernelInterface', $stack);
    }

    protected function getStackedKernelMock()
    {
        $kernel = m::mock('Selene\Components\Stack\StackedKernelInterface');
        return $kernel;
    }

    protected function getKernelMock()
    {
        $kernel = m::mock('Symfony\Component\HttpKernel\HttpKernelInterface');
        return $kernel;
    }
}
