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
use \Selene\Module\Events\Tests\Fixures\PrioritySubscriber;
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
    /** @test */
    public function it_should_bind_a_callable_as_handler()
    {
        $called = false;

        $dispatcher = $this->newDispatcher();

        $dispatcher->on('event', $handler = function () use (&$called) {
            $called = true;
        });

        $dispatcher->dispatch('event');

        $this->assertTrue($called, 'It should have called the handler.');
    }

    /** @test */
    public function it_should_bind_multiple_events_to_one_handler()
    {
        $called = 0;

        $dispatcher = $this->newDispatcher();

        $dispatcher->on(['foo', 'bar'], function () use (&$called) {
            $called++;
        });

        $dispatcher->dispatch('foo');
        $dispatcher->dispatch('bar');

        $this->assertSame(2, $called, 'The handler should have been called twice.');
    }

    /** @test */
    public function it_Should_Bind_Multiple_Events_To_One_Handler_Once()
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

        $this->assertSame(2, $called);
    }

    /** @test */
    public function it_should_be_able_to_dispatch_event_objects()
    {
        $called = false;
        $event = new EventStub;
        $event->setEventName('test');

        $dispatcher = $this->newDispatcher();

        $dispatcher->on('test', function ($evt) use ($event, &$called) {
            $called = true;
            $this->assertSame($evt, $event);
        });

        $dispatcher->dispatchEvent($event);
        $this->assertTrue($called);

        $i = 0;

        $eventB = new EventStub;
        $eventB->setEventName('test_b');

        $eventC = new EventStub;
        $eventC->setEventName('test_c');

        $dispatcher->on('test_b', function ($evt) use (&$i) {
            $i++;
        });

        $dispatcher->on('test_c', function ($evt) use (&$i) {
            $i++;
        });

        $dispatcher->dispatchEvents([$eventB, $eventC]);
        $this->assertSame(2, $i);
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
    public function itShouldDispatchEventsDependingOnTheirPriority()
    {
        $result = [];
        $dispatcher = $this->newDispatcher();

        $dispatcher->on('foo', function () use (&$result) {
            $result[] = 'foo';
        }, 200);

        $dispatcher->on('foo', function () use (&$result) {
            $result[] = 'bar';
        }, 100);

        $dispatcher->on('foo', function () use (&$result) {
            $result[] = 'baz';
        }, 300);

        $dispatcher->dispatch('foo');

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

        $dispatcher->dispatch('foo');
        $dispatcher->dispatch('foo');

        $this->assertTrue(true);
        $this->assertSame(1, $counter);
    }

    /** @test */
    public function it_should_stop_propagation_when_Event_is_stopped()
    {
        $called = [
            'a' => false,
            'b' => false,
            'c' => false,
        ];

        $dispatcher = $this->newDispatcher();
        $dispatcher->on(
            'foo',
            function ($event) use (&$called) {
                $event->stopPropagation();
                $called['a'] = true;
            }
        );

        $dispatcher->on(
            'foo',
            function ($event) use (&$called) {
                $called['b'] = true;
            }
        );

        $dispatcher->on(
            'foo',
            function ($event) use (&$called) {
                $called['c'] = true;
            }
        );

        $dispatcher->dispatch('foo', new EventStub);

        $this->assertTrue($called['a']);
        $this->assertFalse($called['b']);
        $this->assertFalse($called['c']);
    }

    /** @test */
    public function it_should_be_able_to_detach_handlers()
    {
        $dispatcher = $this->newDispatcher();

        $i = 0;
        $dispatcher->on(
            'foo',
            $foo = function () use (&$i) {
                $i++;
                $this->fail();
            }
        );

        $dispatcher->on(
            'foo',
            function () use (&$i) {
                $i++;
            }
        );

        $dispatcher->off('foo', $foo);
        $dispatcher->dispatch('foo');

        $this->assertSame(1, $i);
    }

    /**
     * @test
     */
    public function testDetachEventWithBoundCallable()
    {
        $called = 0;

        $dispatcher = $this->newDispatcher();

        $class = m::mock('HandleAwareClass');

        $class->shouldReceive('handleEvent')->andReturnUsing(
            function () use (&$called) {
                $called++;
                $this->fail();
            }
        );

        $class->shouldReceive('doHandleEvent')->andReturnUsing(
            function () use (&$called) {
                $called++;
                $this->assertTrue(true);
            }
        );

        $dispatcher->on('foo', [$class, 'handleEvent']);
        $dispatcher->on('foo', [$class, 'doHandleEvent']);

        $dispatcher->off('foo', [$class, 'handleEvent']);

        $dispatcher->dispatch('foo');

        $this->assertSame(1, $called);
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

    /** @test */
    public function it_should_be_able_to_add_subscribers()
    {
        $results = [];

        $dispatcher = $this->newDispatcher();
        $dispatcher->addSubscriber(new PrioritySubscriber($results));

        $dispatcher->dispatch('event_a');

        $this->assertSame($a = ['a.pre', 'a.mid', 'a.after'], $results);

        $dispatcher->dispatch('event_b');

        $this->assertSame(array_merge($a, ['b']), $results);
    }

    /** @test */
    public function it_should_be_able_to_remove_subscribers()
    {
        $dispatcher = $this->newDispatcher();

        $dispatcher->addSubscriber($subscriber = new PrioritySubscriber);

        $dispatcher->on('event', $handler = function () {
        });

        $dispatcher->removeSubscriber($subscriber);

        $this->assertSame([$handler], $dispatcher->getEventHandlers());

    }

    /**
     * newDispatcher
     *
     * @param ContainerInterface $container
     *
     * @access protected
     * @return Dispatcher
     */
    protected function newDispatcher(ContainerInterface $container = null)
    {
        return new Dispatcher;
    }

    protected function tearDown()
    {
        m::close();
    }
}
