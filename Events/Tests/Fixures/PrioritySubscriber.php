<?php

/**
 * This File is part of the Selene\Module\Events\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events\Tests\Fixures;

use Selene\Module\Events\SubscriberInterface;

class PrioritySubscriber implements SubscriberInterface
{
    public static $event;

    protected $pool;

    public function __construct(array &$pool = [])
    {
        $this->pool =& $pool;
    }

    public function getSubscriptions()
    {
        return [
            'event_a' => [
                ['onEventAPre', 100],
                ['onEventAMid', 10],
                ['onEventAAfter', 0]
            ],
            'event_b' => ['onEventB', 10]
        ];
    }

    public function onEventAPre()
    {
        $this->pool[] = 'a.pre';
    }

    public function onEventAMid()
    {
        $this->pool[] = 'a.mid';
    }

    public function onEventAAfter()
    {
        $this->pool[] = 'a.after';
    }

    public function onEventB()
    {
        $this->pool[] = 'b';
    }
}
