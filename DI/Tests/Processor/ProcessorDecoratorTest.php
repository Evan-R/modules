<?php

/**
 * This File is part of the Selene\Module\DI\Tests\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests\Processor;

use \Mockery as m;
use \Selene\Module\TestSuite\TestCase;
use \Selene\Module\DI\Processor\ProcessorDecorator;

/**
 * @class ProcessorDecoratorTest
 * @package Selene\Module\DI\Tests\Processor
 * @version $Id$
 */
class ProcessorDecoratorTest extends TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $proc = m::mock('Selene\Module\DI\Processor\ProcessorInterface');

        $this->assertInstanceof('Selene\Module\DI\Processor\ProcessorDecorator', new ProcessorDecorator($proc));
    }

    /** @test */
    public function itShouldThrowExceptionWhenCallingProcess()
    {
        $proc = m::mock('Selene\Module\DI\Processor\ProcessorInterface');
        $decorator = new ProcessorDecorator($proc);

        try {
            $decorator->process(m::mock('Selene\Module\DI\ContainerInterface'));
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
        $proc = m::mock('Selene\Module\DI\Processor\ProcessorInterface');
        $proccess = m::mock('Selene\Module\DI\Processor\ProcessInterface');

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
