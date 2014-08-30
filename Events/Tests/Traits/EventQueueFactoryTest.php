<?php

/**
 * This File is part of the Selene\Module\Events\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events\Tests\Traits;

use \Mockery as m;
use \Selene\Module\Events\Traits\EventQueueFactory;

/**
 * @class EventDispatcherTest extends TestCase
 * @see TestCase
 *
 * @package Selene\Module\Events\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class EventQueueFactoryTest extends \PHPUnit_Framework_TestCase
{
    use EventQueueFactory;

    /** @test */
    public function itShouldQueueAndReleaseEvents()
    {
        $eventA = m::mock('Selene\Module\Events\EventInterface');
        $eventB = m::mock('Selene\Module\Events\EventInterface');

        $this->raiseEvent($eventA);
        $this->raiseEvent($eventB);

        $this->assertSame([$eventA, $eventB], $this->releaseEvents());
    }

    /** @test */
    public function itShouldQueueAndReleaseEventsSorted()
    {
        $eventA = m::mock('Selene\Module\Events\EventInterface');
        $eventB = m::mock('Selene\Module\Events\EventInterface');
        $eventC = m::mock('Selene\Module\Events\EventInterface');

        $this->raiseEvent($eventA, 10);
        $this->raiseEvent($eventB, 100);
        $this->raiseEvent($eventC, 1);

        $this->assertSame([$eventB, $eventA, $eventC], $this->releaseEvents());
    }
}
