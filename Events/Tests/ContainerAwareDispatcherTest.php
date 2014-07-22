<?php

/**
 * This File is part of the Selene\Components\Events\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events\Tests;

use \Mockery as m;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Events\ContainerAwareDispatcher;

/**
 * @class ContainerAwareDispatcherTest
 * @package Selene\Components\Events\Tests
 * @version $Id$
 */
class ContainerAwareDispatcherTest extends DispatcherTest
{
    /**
     * @test
     */
    public function testDetachEventWithBoundCallableClass()
    {
        $class = m::mock('HandleAwareClass');
        $class
            ->shouldReceive('handleEvent')->andReturnUsing(
                function () {
                    $this->fail('event callback `handleEvent` should not be called');
                    return true;
                }
            )
            ->shouldReceive('respond')->andReturnUsing(
                function () {
                    return true;
                }
            );

        $container = m::mock('Selene\Components\DI\ContainerInterface');
        $container->shouldReceive('get')->with('some_service')->andReturn($class);
        $container->shouldReceive('has')->with('some_service')->andReturn(true);

        $dispatcher = $this->newDispatcher($container);

        $dispatcher->on('foo', 'some_service');
        $dispatcher->on('foo', 'some_service@respond');

        $dispatcher->off('foo', 'some_service');

        $result = $dispatcher->dispatch('foo');

        $this->assertSame([true], $result);
    }

    /** @test */
    public function itShouldThrowOnInvalidHandlers()
    {
        $dispatcher = $this->newDispatcher();

        try {
            $dispatcher->on('foo', 'bar');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(
                'Cannot set a service "bar" as handler, no service container is set.',
                $e->getMessage()
            );
        }

        $container = m::mock('Selene\Components\DI\ContainerInterface');
        $container->shouldReceive('has')->with('bar')->andReturn(false);

        $dispatcher->setContainer($container);

        try {
            $dispatcher->once('foo', 'bar');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('A service with id "bar" is not defined.', $e->getMessage());
        }

        try {
            $dispatcher->once('foo', $handler = 'bar@baz@bam');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid event handler "'.$handler.'".', $e->getMessage());

        }

        try {
            $dispatcher->once('foo', new \stdClass);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid event handler "stdClass".', $e->getMessage());

            return;
        }

        $this->giveUp();
    }

    /**
     * newDispatcher
     *
     * @param ContainerInterface $container
     *
     * @return ContainerAwareDispatcher
     */
    protected function newDispatcher(ContainerInterface $container = null)
    {
        return new ContainerAwareDispatcher($container ?: null);
    }

}