<?php

/**
 * This File is part of the Selene\Module\Events\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events\Tests\Stubs;

use Selene\Module\Events\SubscriberInterface;

/**
 * @class EventSubscriberStub
 * @package
 * @version $Id$
 */
class EventSubscriberStub implements SubscriberInterface
{
    public static $event;

    public function getSubscriptions()
    {
        return [
            'foo.event' => [
                ['onFooEventPre', 100],
                ['onFooEventMid', 10],
                ['onFooEventAfter', 0]
            ],
            'bar.event' => ['onBarEvent', 10]
        ];
    }

    public function onFooEventPre()
    {
        return 'foo.pre';
    }

    public function onFooEventMid()
    {
        return 'foo.mid';
    }

    public function onFooEventAfter()
    {
        return 'foo.after';
    }

    public function onBarEvent()
    {
        return 'bar';
    }
}
