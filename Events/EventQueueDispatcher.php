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
 * @class EventQueueDispatcher
 * @package Selene\Components\Events
 * @version $Id$
 */
class EventQueueDispatcher implements EventQueueDispatcherInterface
{
    /**
     * listeners
     *
     * @var array
     */
    private $listeners;

    /**
     * sorted
     *
     * @var array
     */
    private $sorted;

    /**
     * Creates a new Listener dispatcher.
     */
    public function __construct()
    {
        $this->sorted = [];
        $this->listeners = [];
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($events)
    {
        if (!is_array($events)) {
            return $this->doDispatch($events);
        }

        foreach ($events as $event) {
            $this->doDispatch($event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addListener($eventName, EventListenerInteface $listener, $priority = 0)
    {
        unset($this->sorted[$eventName]);

        $this->listeners[$eventName][$priority][] = $listener;
    }

    /**
     * Dispatch an event.
     *
     * @param EventInterface $event
     *
     * @return void
     */
    private function doDispatch(EventInterface $event)
    {
        $name = $event->getEventName();

        if (isset($this->listeners[$name])) {
            $this->fireEvent($name, $event);
        }
    }

    /**
     * Fires the event.
     *
     * @param mixed $eventName
     * @param EventInterface $event
     *
     * @return void
     */
    private function fireEvent($eventName, EventInterface $event)
    {
        foreach ($this->getSorted($eventName) as $listenerArray) {
            foreach ($listenerArray as $listener) {
                $listener->handleEvent($event);
            }
        }
    }

    /**
     * Get the sorted eventlisteners.
     *
     * @param string $eventName
     *
     * @return array
     */
    private function getSorted($eventName)
    {
        if (!isset($this->sorted[$eventName])) {
            // using krsor, higher index, higher priority
            krsort($this->listeners[$eventName], SORT_NUMERIC);
            $this->sorted[$eventName] = true;
        }

        return $this->listeners[$eventName];
    }
}
