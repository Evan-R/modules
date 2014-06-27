<?php

/**
 * This File is part of the Selene\Components\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events;

/**
 * @class EventQueueDispatcherInterface
 * @package Selene\Components\Events
 * @version $Id$
 */
interface EventQueueDispatcherInterface
{
    /**
     * dispatch
     *
     * @access public
     * @return void
     */
    public function dispatch($events);

    /**
     * addListener
     *
     * @param EventListenerInteface $listener
     *
     * @return void
     */
    public function addListener($eventName, EventListenerInteface $listener, $priority = 0);
}
