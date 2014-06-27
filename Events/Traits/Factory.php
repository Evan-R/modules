<?php

/**
 * This File is part of the Selene\Components\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events\Traits;

use \Selene\Components\Events\EventInterface;

/**
 * @class Factory
 * @package Selene\Components\Events
 * @version $Id$
 */
trait Factory
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
