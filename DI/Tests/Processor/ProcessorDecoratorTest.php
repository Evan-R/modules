<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Processor;

use \Mockery as m;
use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\DI\Processor\ProcessorDecorator;

/**
 * @class ProcessorDecoratorTest
 * @package Selene\Components\DI\Tests\Processor
 * @version $Id$
 */
class ProcessorDecoratorTest extends TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $proc = m::mock('Selene\Components\DI\Processor\ProcessorInterface');

        $this->assertInstanceof('Selene\Components\DI\Processor\ProcessorDecorator', new ProcessorDecorator($proc));
    }

    /** @test */
    public function itShouldThrowExceptionWhenCallingProcess()
    {
        $proc = m::mock('Selene\Components\DI\Processor\ProcessorInterface');
        $decorator = new ProcessorDecorator($proc);

        try {
            $decorator->process(m::mock('Selene\Components\DI\ContainerInterface'));
        } catch (\BadMethodCallException $e) {
            $this->assertSame('Calling "process()" is not allowed.', $e->getMessage());

            return;
        }

        $this->giveUp();
    }

    /** @test */
    public function itShouldAllowToAddProcesses()
    {
        $called = false;
        $proc = m::mock('Selene\Components\DI\Processor\ProcessorInterface');
        $proccess = m::mock('Selene\Components\DI\Processor\ProcessInterface');

        $proc->shouldReceive('add')->once()->with($proccess, 0)->andReturnUsing(function ($pro) use (&$called) {
            $called = true;
        });

        $decorator = new ProcessorDecorator($proc);

        $decorator->add($proccess);

        $this->assertTrue($called);
    }

    protected function tearDown()
    {
        m::close();
    }
}
