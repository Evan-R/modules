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
use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\Events\Event;
use \Selene\Components\Events\Dispatcher;
use \Selene\Components\Events\Tests\Stubs\EventStub;
use \Selene\Components\Events\Tests\Stubs\EventSubscriberStub;
use \Selene\Components\DI\ContainerInterface;

/**
 * @class EventDispatcherTest extends TestCase
 * @see TestCase
 *
 * @package Selene\Components\Events\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testBindEvent()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher->on('foo', function () {
            return 'bar';
        });

        $dispatcher->on('foo', function () {
            return 'baz';
        });

        $result = $dispatcher->dispatch('foo');

        $this->assertSame(['bar', 'baz'], $result);
    }

    /** @test */
    public function itShouldBindMultipleEventsToOneHandler()
    {
        $called = 0;

        $dispatcher = $this->createDispatcher();

        $dispatcher->on(['foo', 'bar'], function () use (&$called) {
            $called++;
        });

        $dispatcher->dispatch('foo');
        $dispatcher->dispatch('bar');

        $this->assertSame(2, $called);
    }

    /** @test */
    public function itShouldBindMultipleEventsToOneHandlerOnce()
    {
        $called = 0;

        $dispatcher = $this->createDispatcher();

        $dispatcher->once(['foo', 'bar'], function () use (&$called) {
            $called++;
        });

        $dispatcher->dispatch('foo');
        $dispatcher->dispatch('bar');

        $dispatcher->dispatch('foo');
        $dispatcher->dispatch('bar');

        $dispatcher->dispatch('foo');
        $dispatcher->dispatch('bar');

        $this->assertSame(2, $called);
    }

    /** @test */
    public function itShouldDetachAllHandlers()
    {
        $called = 0;

        $dispatcher = $this->createDispatcher();

        $dispatcher->on('foo', function () use (&$called) {
            $called++;
        });

        $dispatcher->on('foo', function () use (&$called) {
            $called++;
        });

        $dispatcher->off('foo');

        $dispatcher->dispatch('foo');

        $this->assertSame(0, $called);

        $this->assertSame([], $dispatcher->getEventHandlers());
    }

    /** @test */
    public function itShouldAlwaysReturnAnArrayWhenGettingHandlers()
    {
        $dispatcher = $this->createDispatcher();
        $this->assertSame([], $dispatcher->getEventHandlers());

        $dispatcher = $this->createDispatcher();
        $this->assertSame([], $dispatcher->getEventHandlers('foo'));
    }

    /** @test */
    public function itShouldBindEventListeners()
    {
        $called = false;

        $dispatcher = $this->createDispatcher();

        $listener = m::mock('Selene\Components\Events\EventListenerInterface');
        $listener->shouldReceive('handleEvent')->andReturnUsing(function () use (&$called) {
            $called = true;
        });

        $event = m::mock('Selene\Components\Events\\EventInterface');

        $event->shouldReceive('setEventName')->with('event');
        $event->shouldReceive('getEventName')->andReturn('event');
        $event->shouldReceive('isPropagationStopped')->andReturn(false);

        $dispatcher->on('event', $listener);

        $dispatcher->dispatch('event', $event);

        $this->assertTrue($called, 'Listener should be called');
    }

    /** @test */
    public function itShouldBindEventListenersOnce()
    {
        $called = 0;

        $dispatcher = $this->createDispatcher();

        $listener = m::mock('Selene\Components\Events\EventListenerInterface');

        $listener->shouldReceive('handleEvent')->andReturnUsing(function () use (&$called) {
            $called++;
        });

        $event = m::mock('Selene\Components\Events\\EventInterface');

        $event->shouldReceive('setEventName')->with('event');
        $event->shouldReceive('getEventName')->andReturn('event');
        $event->shouldReceive('isPropagationStopped')->andReturn(false);

        $dispatcher->once('event', $listener);

        $dispatcher->dispatch('event', $event);
        $dispatcher->dispatch('event', $event);
        $dispatcher->dispatch('event', $event);

        $this->assertSame(1, $called, 'Listener should be called');
    }


    /** @test */
    public function itShouldDetatchAListener()
    {
        $called = 0;

        $dispatcher = $this->createDispatcher();

        $listenerA = m::mock('Selene\Components\Events\EventListenerInterface');

        $listenerA->shouldReceive('handleEvent')->andReturnUsing(function () use (&$called) {
            $called++;
        });

        $listenerB = m::mock('Selene\Components\Events\EventListenerInterface');

        $listenerB->shouldReceive('handleEvent')->andReturnUsing(function () use (&$called) {
            $called++;
        });

        $event = m::mock('Selene\Components\Events\\EventInterface');

        $event->shouldReceive('setEventName')->with('event');
        $event->shouldReceive('getEventName')->andReturn('event');
        $event->shouldReceive('isPropagationStopped')->andReturn(false);

        $dispatcher->on('event', $listenerA);
        $dispatcher->on('event', $listenerB);

        $dispatcher->dispatch('event', $event);

        $dispatcher->off('event', $listenerA);

        $dispatcher->dispatch('event', $event);

        $dispatcher->off('event', $listenerB);

        $dispatcher->dispatch('event', $event);

        $this->assertSame(3, $called, 'Listener should be called');
    }

    /** @test */
    public function itShouldReturnAllListenersForAEvent()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher->on('foo', $a = function () {
        });
        $dispatcher->on('foo', $b = function () {
        });

        $this->assertSame([$a, $b], $dispatcher->getEventHandlers('foo'));
    }

    /** @test */
    public function itShouldReturnAllListenersForAEventSorted()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher->on('foo', $a = function () {
        }, 10);

        $dispatcher->on('foo', $b = function () {
        }, 100);

        $this->assertSame([$b, $a], $dispatcher->getEventHandlers('foo'));
    }

    /** @test */
    public function itShouldThrowOnInvalidHandlers()
    {
        $dispatcher = $this->createDispatcher();

        try {
            $dispatcher->on('foo', 'bar');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(
                get_class($dispatcher).'::on() expects argument 2 to be valid callback',
                $e->getMessage()
            );
        }

        try {
            $dispatcher->once('foo', 'bar');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(
                get_class($dispatcher).'::once() expects argument 2 to be valid callback',
                $e->getMessage()
            );
            return;
        }

        $this->fail('test failed');
    }

    /**
     * @test
     */
    public function testDispatchPriority()
    {
        $dispatcher = $this->createDispatcher();

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

        $dispatcher = $this->createDispatcher();
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

        $container = m::mock('Selene\Components\DI\ContainerInterface');
        $container->shouldReceive('getService')->with('some_service')->andReturn($class);

        $dispatcher = $this->createDispatcher($container);

        $dispatcher->on('foo', '$some_service');
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

        $container = m::mock('Selene\Components\DI\ContainerInterface');
        $container->shouldReceive('getService')->with('some_service')->andReturn($class);

        $dispatcher = $this->createDispatcher($container);

        $dispatcher->on('foo', '$some_service@doHandle');
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
        $dispatcher = $this->createDispatcher();

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
        $dispatcher = $this->createDispatcher();
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
    public function testDispatchUntil()
    {

        $dispatcher = $this->createDispatcher();

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

        $result = $dispatcher->until('foo');
        $this->assertSame(['boom'], $result);
    }

    /**
     * @test
     */
    public function testDetachEvent()
    {
        $dispatcher = $this->createDispatcher();
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
        $dispatcher = $this->createDispatcher();

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
        $class
            ->shouldReceive('handleEvent')->andReturnUsing(
                function () {
                    $this->fail('event callback `handleEvent` should not be called');
                    return true;
                }
            )
            ->shouldReceive('responde')->andReturnUsing(
                function () {
                    return true;
                }
            );

        $container = m::mock('Selene\Components\DI\ContainerInterface');
        $container->shouldReceive('getService')->with('some_service')->andReturn($class);

        $dispatcher = $this->createDispatcher($container);

        $dispatcher->on('foo', '$some_service');
        $dispatcher->on('foo', '$some_service@responde');
        $dispatcher->off('foo', '$some_service');

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
        $dispatcher = $this->createDispatcher();
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
        $dispatcher = $this->createDispatcher();
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
        $dispatcher = $this->createDispatcher();
        $dispatcher->addSubscriber($observer = new EventSubscriberStub);
        $result = $dispatcher->dispatch('foo.event');

        $dispatcher->removeSubscriber($observer);
        $result = $dispatcher->dispatch('foo.event');
        $this->assertSame([], $result);

        $result = $dispatcher->dispatch('bar.event');
        $this->assertSame([], $result);
    }

    /**
     * createDispatcher
     *
     * @param ContainerInterface $container
     *
     * @access protected
     * @return Dispatcher
     */
    protected function createDispatcher(ContainerInterface $container = null)
    {
        if (null === $container) {
            return new Dispatcher();
        }
        return new Dispatcher($container);
    }
}