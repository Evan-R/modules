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

use \Selene\Components\Events\Tests\Stubs\ConcreteEvent;

/**
 * @class EventDispatcherTest extends TestCase
 * @see TestCase
 *
 * @package Selene\Components\Events\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class EventTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldHaveADefaultName()
    {
        $this->assertEquals('concrete.event', (new ConcreteEvent)->getEventName());
    }

    /** @test */
    public function itShouldSetAName()
    {
        $event = new ConcreteEvent();
        $event->setEventName('foo.bar');

        $this->assertEquals('foo.bar', $event->getEventName());
    }

    /** @test */
    public function itStopEventPropagation()
    {
        $event = new ConcreteEvent();

        $this->assertFalse($event->isPropagationStopped());

        $event->stopPropagation();

        $this->assertTrue($event->isPropagationStopped());
    }
}
