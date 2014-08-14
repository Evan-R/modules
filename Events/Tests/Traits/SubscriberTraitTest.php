<?php

/**
 * This File is part of the Selene\Module\Events\Tests\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events\Tests\Traits;

use \Mockery as m;
use \Selene\Module\Events\SubscriberInterface;
use \Selene\Module\Events\Traits\SubscriberTrait;

/**
 * @class SubscriberTraitTest
 * @package Selene\Module\Events\Tests\Traits
 * @version $Id$
 */
class SubscriberTraitTest extends \PHPUnit_Framework_TestCase implements SubscriberInterface
{
    use SubscriberTrait;

    private static $subscriptions = [
        'foo' => 'onFoo'
    ];

    /** @test */
    public function itShouldSubscribeToADispatcher()
    {
        $subscribed = false;

        $events = m::mock('Selene\Module\Events\Dispatcher');

        $events->shouldReceive('addSubscriber')->with($this)
            ->andReturnUsing(function () use (&$subscribed) {
                $subscribed = true;
            });

        $this->subscribeTo($events);

        $this->assertTrue($subscribed);
    }

    /** @test */
    public function itShouldReturnSubscriptions()
    {
        $this->assertSame(static::$subscriptions, $this->getSubscriptions());
    }

    protected function tearDown()
    {
        m::close();
    }
}
