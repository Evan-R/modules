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

use Mockery as m;
use Selene\Components\TestSuite\TestCase;
use Selene\Components\Events\Dispatcher;
use Selene\Components\Events\Tests\Stubs\EventStub;
use Selene\Components\Events\Tests\Stubs\EventSubscriberStub;
use Selene\Components\DependencyInjection\ContainerInterface;

class EventDispatcherTest extends TestCase
{
    /**
     * @var ClassName
     */
    protected $object;

    /**
     * @test
     */
    public function testBindEvent()
    {
        $dispatcher = new Dispatcher();

        $dispatcher->on(
            'foo',
            function () {
                return 'bar';
            }
        );

        $dispatcher->on(
            'foo',
            function () {
                return 'baz';
            }
        );

        $result = $dispatcher->dispatch('foo');

        $this->assertSame(['bar', 'baz'], $result);
    }

    /**
     * @test
     */
    public function testDispatchPriority()
    {
        $dispatcher = new Dispatcher();

        $dispatcher->on(
            'foo',
            function () {
                return 'foo';
            },
            200
        );

        $dispatcher->on(
            'foo',
            function () {
                return 'bar';
            },
            100
        );

        $dispatcher->on(
            'foo',
            function () {
                return 'baz';
            },
            300
        );

        $result = $dispatcher->dispatch('foo');

        $this->assertSame(['baz', 'foo', 'bar'], $result);
    }

    /**
     * @test
     */
    public function testBindCallable()
    {
        $class = m::mock('HandleAwareClass');
        $class->shouldReceive('doHandleEvent')->andReturn(true);

        $dispatcher = new Dispatcher();
        $dispatcher->on('foo', [$class, 'doHandleEvent']);
        $result = $dispatcher->dispatch('foo');

        $this->assertTrue(current($result));
    }

    /**
     * @test
     */
    public function testBindClassDefinitionShouldCallHandleEvent()
    {
        $class = m::mock('HandleAwareClass');
        $class->shouldReceive('handleEvent')->andReturnUsing(
            function () {
                $this->assertTrue(true);

                return true;
            }
        );

        $container = m::mock('Selene\Components\DependencyInjection\ContainerInterface');
        $container->shouldReceive('offsetGet')->with('HandleAwareClass')->andReturn($class);

        $dispatcher = new Dispatcher($container);

        $dispatcher->on('foo', 'HandleAwareClass');
        $result = $dispatcher->dispatch('foo');

        if (empty($result)) {
            $this->fail();
        }
    }

    /**
     * @test
     */
    public function testBindClassDefinitionShouldCallDefinedMethod()
    {
        $class = m::mock('HandleAwareClass');
        $class->shouldReceive('doHandle')->andReturnUsing(
            function () {
                $this->assertTrue(true);

                return true;
            }
        );

        $container = m::mock('Selene\Components\DependencyInjection\ContainerInterface');
        $container->shouldReceive('offsetGet')->with('HandleAwareClass')->andReturn($class);

        $dispatcher = new Dispatcher($container);

        $dispatcher->on('foo', 'HandleAwareClass@doHandle');
        $result = $dispatcher->dispatch('foo');

        if (empty($result)) {
            $this->fail();
        }
    }

    /**
     * @test
     */
    public function testBindOnce()
    {
        $counter = 0;
        $dispatcher = new Dispatcher();

        $dispatcher->once(
            'foo',
            function () use (&$counter) {
                $counter++;
                if ($counter > 1) {
                    $this->fail();
                }
            }
        );

        $result = $dispatcher->dispatch('foo');
        $result = $dispatcher->dispatch('foo');
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function testStopEventPropagation()
    {
        $dispatcher = new Dispatcher();
        $dispatcher->on(
            'foo',
            function ($event) {
                $event->stopPropagation();
                return 'bar';
            }
        );

        $dispatcher->on(
            'foo',
            function () {
                return 'baz';
            }
        );

        $dispatcher->on(
            'foo',
            function () {
                return 'boom';
            }
        );

        $result = $dispatcher->dispatch('foo', new EventStub);
        $this->assertSame(['bar'], $result);
    }

    /**
     * @test
     */
    public function testDispatchUntill()
    {

        $dispatcher = new Dispatcher();

        $dispatcher->on(
            'foo',
            function () {
                return;
            }
        );

        $dispatcher->on(
            'foo',
            function () {
                return;
            }
        );

        $dispatcher->on(
            'foo',
            function () {
                return 'boom';
            }
        );

        $dispatcher->on(
            'foo',
            function () {
                return 'bam';
            }
        );

        $result = $dispatcher->untill('foo');
        $this->assertSame(['boom'], $result);
    }

    /**
     * @test
     */
    public function testDetachEvent()
    {
        $dispatcher = new Dispatcher();
        $dispatcher->on(
            'foo',
            $foo = function () {
                $this->fail();
                return 'bar';
            }
        );

        $dispatcher->on(
            'foo',
            function () {
                return 'baz';
            }
        );

        $dispatcher->off('foo', $foo);
        $result = $dispatcher->dispatch('foo');
        $this->assertSame(['baz'], $result);
    }

    /**
     * @test
     */
    public function testDetachEventWithBoundCallable()
    {
        $dispatcher = new Dispatcher();

        $class = m::mock('HandleAwareClass');
        $class->shouldReceive('handleEvent')->andReturnUsing(
            function () {
                $this->fail();
                return true;
            }
        );

        $class->shouldReceive('doHandleEvent')->andReturnUsing(
            function () {
                $this->assertTrue(true);
                return true;
            }
        );

        $dispatcher->on('foo', [$class, 'handleEvent']);
        $dispatcher->on('foo', [$class, 'doHandleEvent']);

        $dispatcher->off('foo', [$class, 'handleEvent']);
        $result = $dispatcher->dispatch('foo');
        $this->assertSame([true], $result);
    }

    /**
     * @test
     */
    public function testDetachEventWithBoundCallableClass()
    {
        $class = m::mock('HandleAwareClass');
        $class->shouldReceive('handleEvent')->andReturnUsing(
            function () {
                $this->fail();
                return true;
            }
        );

        $class->shouldReceive('doHandleEvent')->andReturnUsing(
            function () {
                return true;
            }
        );

        $container = m::mock('Selene\Components\DependencyInjection\ContainerInterface');
        $container->shouldReceive('offsetGet')->with('HandleAwareClass')->andReturn($class);

        $dispatcher = new Dispatcher($container);

        $dispatcher->on('foo', 'HandleAwareClass');
        $dispatcher->on('foo', 'HandleAwareClass@doHandleEvent');
        $dispatcher->off('foo', 'HandleAwareClass');

        $result = $dispatcher->dispatch('foo');
        $this->assertSame([true], $result);
    }

    public function testGetAllHandlers()
    {
        $foo = function () {
        };
        $bar = function () {
        };
        $baz = function () {
        };
        $dispatcher = new Dispatcher();
        $dispatcher->on('foo', $foo);
        $dispatcher->on('bar', $bar);
        $dispatcher->on('baz', $baz);

        $handlers = $dispatcher->getEventHandlers();

        $this->assertSame([$foo, $bar, $baz], $handlers);
    }

    /**
     * @test
     */
    public function testAddSubscriber()
    {
        $dispatcher = new Dispatcher();
        $dispatcher->addSubscriber(new EventSubscriberStub);
        $result = $dispatcher->dispatch('foo.event');

        $this->assertSame(['foo.pre', 'foo.mid', 'foo.after'], $result);

        $result = $dispatcher->dispatch('bar.event');
        $this->assertSame(['bar'], $result);
    }
    /**
     * @test
     */
    public function testRemoveObserver()
    {
        $dispatcher = new Dispatcher();
        $dispatcher->addSubscriber($observer = new EventSubscriberStub);
        $result = $dispatcher->dispatch('foo.event');

        $dispatcher->removeSubscriber($observer);
        $result = $dispatcher->dispatch('foo.event');
        $this->assertSame([], $result);

        $result = $dispatcher->dispatch('bar.event');
        $this->assertSame([], $result);
    }
}
