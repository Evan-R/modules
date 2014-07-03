<?php

/**
 * This File is part of the Selene\Components\Events\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events\Tests\Stubs;

use Selene\Components\Events\SubscriberInterface;

/**
 * @class InvalidSubscriber
 * @package Selene\Components\Events\Tests\Stubs
 * @version $Id$
 */
class InvalidSubscriber implements SubscriberInterface
{
    public function getSubscriptions()
    {
        return [
            'event' => 'invalidMethodCall'
        ];
    }
}
