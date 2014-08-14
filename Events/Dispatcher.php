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

use \Selene\Module\Common\Traits\Getter;

/**
 * @class Dispatcher implements DispatcherInterface
 * @see DispatcherInterface
 *
 * @package Selene\Module\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Dispatcher implements DispatcherInterface
{
    use Getter;

    /**
     * events
     *
     * @var array;
     */
    private $handlers = [];

    /**
     * sorted
     *
     * @var array
     */
    private $sorted = [];

    /**
     * handler
     *
     * @var string
     */
    private static $handler = 'eventHandler';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->sorted = [];
        $this->handlers = [];
    }

    /**
     * Attach an eventhandler.
     *
     * Eventhandlers can be any valid callable objects, including Objects
     * implementing `EventListenerInterface`. If you use the dispatcher with a
     * DIC, attaching services as handler is also possible. e.g.
     * `myservice@handleStuff` or just `myservice` if the services implements
     * `EventListenerInterface`.
     *
     * @param string|array $events       the event name
     * @param mixed        $eventHandler a callable or string `service@method`
     * @param int          $priority     the priority with the handler being called
     * (handlers with higher values will be called first)
     *
     * @return void
     */
    public function on($events, $eventHandler, $priority = null)
    {
        foreach ((array)$events as $event) {
            $this->registerEvent($event, $eventHandler, $priority);
        }
    }

    /**
     * Attach an eventhandler just once.
     *
     * Unbinds the eventhandler once the event was fired;
     *
     * @param string $event        the event name
     * @param mixed  $eventHandler a callable or string `class@method`
     * @param int    $priority     the priority with the handler being called
     *
     * @return void
     */
    public function once($events, $eventHandler, $priority = 10)
    {
        foreach ((array)$events as $event) {
            $this->registerOnce($event, $eventHandler, $priority);
        }
    }

    /**
     * Attach a event listener. The Listener must implement
     * `EventListenerInterface`. This is just like `DispatcherInterface::on()` but more explicit.
     *
     * @param string $event                    the event name as string.
     * @param EventListenerInterface $listener the event listener.
     * @param void $priority                   the dispatch priority.
     *
     * @return void
     */
    public function addListener($event, EventListenerInterface $listener, $priority = 0)
    {
        $this->bindEvent($event, $listener, $priority);
    }

    /**
     * Detach an eventhandler from an event.
     *
     * If no event handler is given, all events that are registered under the
     * given event name will be cancled.
     *
     * @param array|string $events       the event name
     * @param mixed        $eventHanlder the eventhandler previously attached
     * the the event.
     *
     * @return void
     */
    public function off($events, $eventHandler = null)
    {
        foreach ((array)$events as $event) {

            if (null !== $eventHandler && $this->detachEventByHandler($event, $eventHandler)) {
                continue;
            }

            unset($this->handlers[$event]);
        }
    }

    /**
     * Registers an event subscriber object.
     *
     * Also see `Selene\Module\Events\SubscriberInterface`
     *
     * @param SubscriberInterface $subscriber the subscriber object.
     *
     * @return void
     */
    public function addSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscriptions() as $event => $handles) {
            foreach ($this->listSubscriptions($subscriber, $event, (array)$handles) as $subscription) {
                call_user_func_array([$this, 'on'], $subscription);
            }
        }
    }

    /**
     * Detaches an event subscriber object.
     *
     * @param SubscriberInterface $subscriber the event subscriber.
     *
     * @return void
     */
    public function removeSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscriptions() as $event => $handles) {
            foreach ($this->listSubscriptions($subscriber, $event, (array)$handles) as $subscription) {
                array_pop($subscription);
                call_user_func_array([$this, 'off'], $subscription);
            }
        }
    }

    /**
     * Dispatches an event.
     *
     * @param string $event      the event name
     * @param mixed  $parameters data to be send along with the event,
     * typically an EventInteface instance
     * @param bool $stopOnFirstResult stop firing if first result was found
     *
     * @return array the event results;
     */
    public function dispatch($eventName, EventInterface $event = null, $stopOnFirstResult = false)
    {
        if (!isset($this->handlers[$eventName])) {
            return;
        }

        $results = [];

        if (null === $event) {
            $event = new Event;
        }

        $event->setEventDispatcher($this);
        $event->setEventName($eventName);

        $handlers = $this->getSorted($eventName);

        foreach ($handlers as &$handlers) {

            if (!$this->doDispatch($handlers, $event, $results, $stopOnFirstResult)) {
                return $results;
            }
        }

        return $results;
    }

    /**
     * Dispatches an event until the first response happened.
     *
     * @see Dispatcher#dispatch()
     *
     * @return array
     */
    public function until($eventName, EventInterface $event = null)
    {
        return $this->dispatch($eventName, $event, true);
    }

    /**
     * Get event handlers by event name
     *
     * @param string $event the event name
     *
     * @return array
     */
    public function getEventHandlers($event = null)
    {
        $handlers = [];

        if (null === $event) {
            foreach ($this->handlers as $event => $eventHandlers) {
                $handlers = array_merge($handlers, $this->getEventHandlers($event));
            }
        } elseif (isset($this->handlers[$event])) {

            foreach ($this->getSorted($event) as $priority => $eventHandlers) {
                $handlers = array_merge($handlers, $eventHandlers);
            }
        }

        return $handlers;
    }

    /**
     * extractEventHandler
     *
     * @param mixed $eventHandler
     *
     * @return array|EventListenerInterface
     */
    protected function extractEventHandler($eventHandler)
    {
        if ($eventHandler instanceof EventListenerInterface || is_callable($eventHandler)) {
            return $eventHandler;
        }

        if (!is_string($eventHandler) || 1 < substr_count($eventHandler, static::EVTHANDLER_SEPARATOR)) {
            // when adding a subscriber, prepare the exception message if the
            // method is not callable:
            if (is_array($eventHandler) && is_object(current($eventHandler))) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid event handler "%s::%s()".',
                        get_class($eventHandler[0]),
                        isset($eventHandler[1]) ? $eventHandler[1] : null
                    )
                );
            }

            // Otherwise prepare a less specific exception message:
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid event handler "%s".',
                    is_string($eventHandler) ? $eventHandler : (is_object($eventHandler) ?
                    get_class($eventHandler) : gettype($eventHandler))
                )
            );
        }

        list($service, $method) = array_pad(explode(static::EVTHANDLER_SEPARATOR, $eventHandler), 2, null);

        $this->handleContainerException($service);

        return [$service, $method];
    }

    /**
     * handleContainerException
     *
     * @param string $service
     *
     * @return void
     */
    protected function handleContainerException($service)
    {
        if (!$this->hasService($service)) {
            throw new \InvalidArgumentException(sprintf('A service with id "%s" is not defined.', $service));
        }
    }

    /**
     * resolveHandler
     *
     * @param mixed $handler
     *
     * @return callable|EventListenerInterface
     */
    protected function resolveHandler($handler)
    {
        if ($handler instanceof EventListenerInterface || is_callable($handler)) {
            return $handler;
        }

        list ($service, $method) = $handler;

        $object = $this->getService($service);

        if ($object instanceof EventListenerInterface && null === $method) {
            return $object;
        } elseif (null === $method) {
            throw new \InvalidArgumentException(
                sprintf('No callable method on service "%s".', $service)
            );
        }

        return [$object, $method];
    }

    /**
     * hasService
     *
     * @param string $id
     *
     * @return boolean
     */
    protected function hasService($id)
    {
        return false;
    }

    /**
     * getService
     *
     * @param string $id
     *
     * @return mixed
     */
    protected function getService($id)
    {
    }

    /**
     * listObserverSubscriptions
     *
     * @param mixed $observer
     * @param mixed $event
     * @param array $eventSubscriptions
     *
     * @return array
     */
    private function listSubscriptions($subscriber, $event, array $eventSubscriptions, array &$list = [])
    {
        if (is_string(current($eventSubscriptions))) {
            list ($method, $priority) = array_pad($eventSubscriptions, 2, 10);

            $list[] = [$event, [$subscriber, $method], $priority];

        } else {
            foreach ($eventSubscriptions as $subscription) {
                $this->listSubscriptions($subscriber, $event, $subscription, $list);
            }
        }

        return $list;
    }

    /**
     * registerEvent
     *
     * @param string  $event
     * @param mixed   $eventHandlers
     * @param integer $priority
     *
     * @return void
     */
    private function registerEvent($event, $eventHandler, $priority = 0)
    {
        $eventHandler = $this->extractEventHandler($eventHandler);

        if ($eventHandler instanceof EventListenerInterface) {
            return $this->addListener($event, $eventHandler, $priority);
        }

        $this->bindEvent($event, $eventHandler, $priority);
    }

    /**
     * registerOnce
     *
     * @param string $event
     * @param mixed  $eventHandler
     * @param int    $priority
     *
     * @return void
     */
    private function registerOnce($event, $eventHandler, $priority = 10)
    {
        if ($eventHandler instanceof EventListenerInterface) {
            return $this->addListenerOnce($event, $eventHandler, $priority);
        }

        $handler = $this->extractEventHandler($eventHandler);

        $eventHandler = function () use ($event, &$handler, &$eventHandler) {

            $this->off($event, $eventHandler);

            return call_user_func_array($handler, func_get_args());
        };

        $this->bindEvent($event, $eventHandler, $priority);
    }

    /**
     * addListenerOnce
     *
     * @param mixed $eventName
     * @param EventListenerInterface $eventHandler
     * @param int $priority
     *
     * @return void
     */
    private function addListenerOnce($eventName, EventListenerInterface $eventHandler, $priority = 0)
    {
        $fn = function (EventInterface $event) use ($eventName, $eventHandler, &$fn) {

            $this->off($eventName, $fn);

            return $eventHandler->handleEvent($event);
        };

        $this->bindEvent($eventName, $fn, $priority);
    }

    /**
     * Bind the Eventhandler to the dispatcher
     *
     * @param string    $event        the event name
     * @param callable  $eventHandler a valid eventhandler
     * @param int       $priority     event fire priority
     *
     * @return void
     */
    private function bindEvent($event, &$eventHandler, $priority = 0)
    {
        unset($this->sorted[$event]);

        $this->handlers[$event][(int)$priority][] = $eventHandler;
    }

    /**
     * doDispatch
     *
     * @param array           $handlers
     * @param EventInterface  $event
     * @param array           $results
     * @param boolean         $stopOnFirstResult
     *
     * @return boolean
     */
    private function doDispatch(array $handlers, EventInterface $event, &$results = [], $stopOnFirstResult = false)
    {
        foreach ($handlers as $index => &$handler) {

            if ($event->isPropagationStopped()) {
                break;
            }

            $res = $this->callListener($handler, $event);

            if (null !== $res) {
                $results[] = $res;

                // stop on first result;
                if ($stopOnFirstResult) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * callListener
     *
     * @param mixed $eventHandler
     * @param mixed $parameters
     *
     * @return mixed
     */
    private function callListener($eventHandler, EventInterface $event)
    {
        $eventHandler = $this->resolveHandler($eventHandler);

        if ($eventHandler instanceof EventListenerInterface) {
            return $eventHandler->handleEvent($event);
        } else {
            return call_user_func_array($eventHandler, [$event]);
        }
    }

    /**
     * Sort eventhandler by their priority.
     *
     * @return array
     */
    private function &getSorted($event)
    {
        if (!isset($this->sorted[$event])) {
            krsort($this->handlers[$event]);
        }

        $this->sorted[$event] = true;

        return $this->handlers[$event];
    }

    /**
     * Finds a matching handler and unsets the handler for the given event.
     *
     * @param string    $event        the event name
     * @param callable  $eventHandler the event handler
     *
     * @return boolean always returns true
     */
    private function detachEventByHandler($event, $eventHandler)
    {
        $eventHandler = $this->extractEventHandler($eventHandler);

        foreach ($this->handlers[$event] as $priority => &$handlers) {
            foreach ($handlers as $index => &$handler) {

                if ($eventHandler === $handler) {
                    unset($this->handlers[$event][$priority][$index]);
                }
            }
        }

        return true;
    }
}
