<?php

/**
 * This File is part of the Selene\Module\Events\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events\Tests;

use \Mockery as m;
use \Selene\Module\TestSuite\TestCase;
use \Selene\Module\Events\Event;
use \Selene\Module\Events\Dispatcher;
use \Selene\Module\Events\Tests\Stubs\EventStub;
use \Selene\Module\Events\Tests\Stubs\EventSubscriberStub;
use \Selene\Module\Events\Tests\Stubs\InvalidSubscriber;
use \Selene\Module\DI\ContainerInterface;

/**
 * @class EventDispatcherTest extends TestCase
 * @see TestCase
 *
 * @package Selene\Module\Events\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class DispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function testBindEvent()
    {
        $dispatcher = $this->newDispatcher();

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

        $dispatcher = $this->newDispatcher();

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

        $dispatcher = $this->newDispatcher();

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

        $dispatcher = $this->newDispatcher();

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

        $called = 0;

        $handler = function () use (&$called) {
            $called++;
        };

        $dispatcher->on('bar', $handler);
        $dispatcher->on('bar', $handler);

        $dispatcher->off('bar', $handler);

        $dispatcher->dispatch('bar');

        $this->assertSame(0, $called);
    }

    /** @test */
    public function itShouldAlwaysReturnAnArrayWhenGettingHandlers()
    {
        $dispatcher = $this->newDispatcher();
        $this->assertSame([], $dispatcher->getEventHandlers());

        $dispatcher = $this->newDispatcher();
        $this->assertSame([], $dispatcher->getEventHandlers('foo'));
    }

    /** @test */
    public function itShouldBindEventListeners()
    {
        $called = false;

        $dispatcher = $this->newDispatcher();

        $listener = m::mock('Selene\Module\Events\EventListenerInterface');
        $listener->shouldReceive('handleEvent')->andReturnUsing(function () use (&$called) {
            $called = true;
        });

        $event = m::mock('Selene\Module\Events\\EventInterface');

        $event->shouldReceive('setEventDispatcher')->with($dispatcher);
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

        $dispatcher = $this->newDispatcher();

        $listener = m::mock('Selene\Module\Events\EventListenerInterface');

        $listener->shouldReceive('handleEvent')->andReturnUsing(function () use (&$called) {
            $called++;
        });

        $event = m::mock('Selene\Module\Events\\EventInterface');

        $event->shouldReceive('setEventDispatcher')->with($dispatcher);
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

        $dispatcher = $this->newDispatcher();

        $listenerA = m::mock('Selene\Module\Events\EventListenerInterface');

        $listenerA->shouldReceive('handleEvent')->andReturnUsing(function () use (&$called) {
            $called++;
        });

        $listenerB = m::mock('Selene\Module\Events\EventListenerInterface');

        $listenerB->shouldReceive('handleEvent')->andReturnUsing(function () use (&$called) {
            $called++;
        });

        $event = m::mock('Selene\Module\Events\\EventInterface');

        $event->shouldReceive('setEventDispatcher')->with($dispatcher);
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
        $dispatcher = $this->newDispatcher();

        $dispatcher->on('foo', $a = function () {
        });
        $dispatcher->on('foo', $b = function () {
        });

        $this->assertSame([$a, $b], $dispatcher->getEventHandlers('foo'));
    }

    /** @test */
    public function itShouldReturnAllListenersForAEventSorted()
    {
        $dispatcher = $this->newDispatcher();

        $dispatcher->on('foo', $a = function () {
        }, 10);

        $dispatcher->on('foo', $b = function () {
        }, 100);

        $this->assertSame([$b, $a], $dispatcher->getEventHandlers('foo'));
    }

    /** @test */
    public function itShouldSetItsselfToAnEvent()
    {
        $dispatcherSet = false;
        $dispatcher = $this->newDispatcher();

        $event = m::mock('Selene\Module\Events\\EventInterface');

        $event->shouldReceive('setEventDispatcher')->with($dispatcher)
            ->andReturnUsing(function ($dsp) use (&$dispatcherSet, $dispatcher) {
                $this->assertSame($dsp, $dispatcher);
                $dispatcherSet = true;
            });
        $event->shouldReceive('setEventName')->with('foo');
        $event->shouldReceive('getEventName')->andReturn('foo');
        $event->shouldReceive('isPropagationStopped')->andReturn(false);

        $dispatcher->on('foo', function ($event) {

        });

        $dispatcher->dispatch('foo', $event);

        $this->assertTrue($dispatcherSet);
    }

    /** @test */
    public function itShouldThrowOnInvalidServiceMethod()
    {
        $dispatcher = $this->newDispatcher();

        $container = m::mock('Selene\Module\DI\ContainerInterface');
        $container->shouldReceive('has')->with('bar')->andReturn(true);
        $container->shouldReceive('get')->with('bar')->andReturn(new \stdClass);

        //$container->shouldReceive('has')->with('baz')->andReturn(true);
        //$container->shouldReceive('get')->with('baz')->andReturn(new \StdClass);

        $dispatcher->setContainer($container);

        $dispatcher->on('foo', 'bar');

        try {
            $dispatcher->dispatch('foo');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('No callable method on service "bar".', $e->getMessage());

            return;
        }

        $this->giveUp();
    }

    /** @test */
    public function itShouldDispatchEventsDependingOnTheirPriority()
    {
        $dispatcher = $this->newDispatcher();

        $dispatcher->on('foo', function () {
            return 'foo';
        }, 200);

        $dispatcher->on('foo', function () {
            return 'bar';
        }, 100);

        $dispatcher->on('foo', function () {
            return 'baz';
        }, 300);

        $result = $dispatcher->dispatch('foo');

        $this->assertSame(['baz', 'foo', 'bar'], $result);
    }

    /** @test */
    public function isShouldHandleACallableClassMethod()
    {
        $called = false;

        $event = new Event;
        $dispatcher = $this->newDispatcher();

        $class = m::mock('HandleAwareClass');
        $class->shouldReceive('doHandleEvent')
            ->once()
            ->with($event)
            ->andReturnUsing(function () use (&$called) {
                $called = true;
            });

        $dispatcher->on('foo', [$class, 'doHandleEvent']);
        $dispatcher->dispatch('foo', $event);

        $this->assertTrue($called, 'HandleAwareClass::doHandleEvent() should be called');
    }

    /** @test */
    public function bindClassDefinitionShouldCallHandleEvent()
    {
        $class = m::mock('HandleAwareClass');
        $class->shouldReceive('handleEvent')->andReturnUsing(
            function () {
                $this->assertTrue(true);

                return true;
            }
        );

        $container = m::mock('Selene\Module\DI\ContainerInterface');
        $container->shouldReceive('get')->with('some_service')->andReturn($class);
        $container->shouldReceive('has')->with('some_service')->andReturn(true);


        $dispatcher = $this->newDispatcher($container);

        $dispatcher->on('foo', 'some_service@handleEvent');

        $result = $dispatcher->dispatch('foo');

        if (empty($result)) {
            $this->fail('event results should not be empty.');
        }
    }

    /** @test */
    public function boundListenersShouldBeDispatched()
    {
        $event = new Event;

        $class = m::mock('Selene\Module\Events\EventListenerInterface');
        $class->shouldReceive('handleEvent')->with($event)->andReturnUsing(
            function () {
                $this->assertTrue(true);

                return true;
            }
        );

        $container = m::mock('Selene\Module\DI\ContainerInterface');
        $container->shouldReceive('get')->with('some_service')->andReturn($class);
        $container->shouldReceive('has')->with('some_service')->andReturn(true);

        $dispatcher = $this->newDispatcher($container);

        $dispatcher->on('foo', 'some_service');

        $result = $dispatcher->dispatch('foo', $event);

        if (empty($result)) {
            $this->fail('event results should not be empty.');
        }
    }

    /** @test */
    public function bindingAServiceDefinitionShouldCallDefinedMethod()
    {
        $class = m::mock('HandleAwareClass');
        $class->shouldReceive('doHandle')->andReturnUsing(
            function () {
                $this->assertTrue(true);

                return true;
            }
        );

        $container = m::mock('Selene\Module\DI\ContainerInterface');
        $container->shouldReceive('get')->with('some_service')->andReturn($class);
        $container->shouldReceive('has')->with('some_service')->andReturn(true);

        $dispatcher = $this->newDispatcher($container);

        $dispatcher->on('foo', 'some_service@doHandle');
        $result = $dispatcher->dispatch('foo');

        if (empty($result)) {
            $this->giveUp();
        }
    }

    /** @test */
    public function testBindOnce()
    {
        $counter = 0;
        $dispatcher = $this->newDispatcher();

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
        $dispatcher = $this->newDispatcher();
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

        $dispatcher = $this->newDispatcher();

        $dispatcher->on('foo', function () {
            return;
        });

        $dispatcher->on('foo', function () {
            return;
        });

        $dispatcher->on('foo', function () {
            return 'boom';
        });

        $dispatcher->on('foo', function () {
            return 'bam';
        });

        $result = $dispatcher->until('foo');

        $this->assertSame(['boom'], $result);
    }

    /**
     * @test
     */
    public function testDetachEvent()
    {
        $dispatcher = $this->newDispatcher();
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
        $dispatcher = $this->newDispatcher();

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

    public function testGetAllHandlers()
    {
        $foo = function () {
        };
        $bar = function () {
        };
        $baz = function () {
        };

        $dispatcher = $this->newDispatcher();
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
        $dispatcher = $this->newDispatcher();
        $dispatcher->addSubscriber(new EventSubscriberStub);
        $result = $dispatcher->dispatch('foo.event');

        $this->assertSame(['foo.pre', 'foo.mid', 'foo.after'], $result);

        $result = $dispatcher->dispatch('bar.event');
        $this->assertSame(['bar'], $result);
    }

    /** @test */
    public function itShouldFilterOutInvalidSubscriptions()
    {
        $dispatcher = $this->newDispatcher();
        $subs = new InvalidSubscriber;

        try {
            $dispatcher->addSubscriber($subs);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid event handler "'.get_class($subs).'::invalidMethodCall()".', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function testRemoveObserver()
    {
        $dispatcher = $this->newDispatcher();
        $dispatcher->addSubscriber($observer = new EventSubscriberStub);
        $result = $dispatcher->dispatch('foo.event');

        $dispatcher->removeSubscriber($observer);
        $result = $dispatcher->dispatch('foo.event');
        $this->assertSame([], $result);

        $result = $dispatcher->dispatch('bar.event');
        $this->assertSame([], $result);
    }

    protected function tearDown()
    {
        m::close();
    }
}
