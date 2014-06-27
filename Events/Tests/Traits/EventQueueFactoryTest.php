<?php

/**
 * This File is part of the Selene\Components\Events\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events\Tests\Traits;

use \Mockery as m;
use \Selene\Components\Events\Traits\EventQueueFactory;

/**
 * @class EventDispatcherTest extends TestCase
 * @see TestCase
 *
 * @package Selene\Components\Events\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class EventQueueFactoryTest extends \PHPUnit_Framework_TestCase
{
    use EventQueueFactory;

    /** @test */
    public function itShouldQueueAndReleaseEvents()
    {
        $eventA = m::mock('Selene\Components\Events\EventInterface');
        $eventB = m::mock('Selene\Components\Events\EventInterface');

        $this->raiseEvent($eventA);
        $this->raiseEvent($eventB);

        $this->assertSame([$eventA, $eventB], $this->releaseEvents());
    }
}
