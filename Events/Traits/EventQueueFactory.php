<?php

/**
 * This File is part of the Selene\Module\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events\Traits;

use \Selene\Module\Events\EventInterface;

/**
 * @class Factory
 * @package Selene\Module\Events
 * @version $Id$
 */
trait EventQueueFactory
{
    /**
     * queuedEvents
     *
     * @var array
     */
    private $queuedEvents = [];

    /**
     * releaseEvents
     *
     * @return array
     */
    public function releaseEvents()
    {
        $events = $this->queuedEvents;
        $this->queuedEvents = [];

        return $events;
    }

    /**
     * raiseEvent
     *
     * @param EventInterface $event
     *
     * @return void
     */
    protected function raiseEvent(EventInterface $event)
    {
        $this->queuedEvents[] = $event;
    }
}
