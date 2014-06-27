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
use \Selene\Components\Events\ListenerDispatcher;
use \Selene\Components\Events\Tests\Stubs\ConcreteEvent;

/**
 * @class EventDispatcherTest extends TestCase
 * @see TestCase
 *
 * @package Selene\Components\Events\Tests
 * @version $Id$
 */
class ListenerDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Components\Events\ListenerDispatcherInterface', new ListenerDispatcher);
    }

    /** @test */
    public function itShouldAddListeners()
    {
        $pass = false;

        $event = $this->getEvent('foo.bar');

        $listener = $this->getListener();

        $listener->shouldReceive('handleEvent')->with($event)->andReturnUsing(function ($event) use (&$pass) {
            $pass = true;
        });

        $dispatcher = new ListenerDispatcher;

        $dispatcher->addListener('foo.bar', $listener);
        $dispatcher->dispatch($event);

        $this->assertTrue($pass, 'Event "foo.bar" should be handled by a listener');
    }

    /** @test */
    public function itShouldDispatchEventsInOrder()
    {
        $sequence = [];

        $event = $this->getEvent('event');

        $listenerA = $this->getListener();
        $listenerB = $this->getListener();
        $listenerC = $this->getListener();

        $listenerA->shouldReceive('handleEvent')->with($event)->andReturnUsing(function ($event) use (&$sequence) {
            $sequence[] = 'a';
        });

        $listenerB->shouldReceive('handleEvent')->with($event)->andReturnUsing(function ($event) use (&$sequence) {
            $sequence[] = 'b';
        });

        $listenerC->shouldReceive('handleEvent')->with($event)->andReturnUsing(function ($event) use (&$sequence) {
            $sequence[] = 'c';
        });

        $dispatcher = new ListenerDispatcher;

        $dispatcher->addListener('event', $listenerC, 20);
        $dispatcher->addListener('event', $listenerA, 100);
        $dispatcher->addListener('event', $listenerB, 50);

        $dispatcher->dispatch($event);

        $this->assertSame(['a', 'b', 'c'], $sequence);
    }

    /** @test */
    public function itShouldDispatchAnArrayOfEvents()
    {

        $eventA = $this->getEvent('event.a');
        $eventB = $this->getEvent('event.b');
        $eventC = $this->getEvent('event.c');

        $sequence = [];

        $listenerA = $this->getListener();
        $listenerB = $this->getListener();

        $listenerA->shouldReceive('handleEvent')->andReturnUsing(function ($event) use (&$sequence) {
            $sequence[] = $event;
        });

        $listenerB->shouldReceive('handleEvent')->andReturnUsing(function ($event) use (&$sequence) {
            $sequence[] = $event;
        });

        $dispatcher = new ListenerDispatcher;

        $dispatcher->addListener('event.a', $listenerA);
        $dispatcher->addListener('event.b', $listenerB);
        $dispatcher->addListener('event.c', $listenerB);

        $dispatcher->dispatch($events = [$eventA, $eventB, $eventC]);

        $this->assertEquals($events, $sequence);
    }

    protected function getEvent($name)
    {
        $event = m::mock('Selene\Components\Events\EventInterface');
        $event->shouldReceive('getEventName')->andReturn($name);

        return $event;
    }

    protected function getListener()
    {
        return m::mock('Selene\Components\Events\EventListenerInteface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
