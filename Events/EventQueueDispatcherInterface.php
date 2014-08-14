<?php

/**
 * This File is part of the Selene\Module\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events;

/**
 * @class EventQueueDispatcherInterface
 * @package Selene\Module\Events
 * @version $Id$
 */
interface EventQueueDispatcherInterface
{
    /**
     * Dispatch events.
     *
     * @param array|EventInterface $events an event or an array of events
     *
     * @return void
     */
    public function dispatch($events);

    /**
     * addListener
     *
     * @param string $eventName
     * @param EventListenerInteface $listener
     * @param int $priority
     *
     * @return void
     */
    public function addListener($eventName, EventListenerInteface $listener, $priority = 0);
}
