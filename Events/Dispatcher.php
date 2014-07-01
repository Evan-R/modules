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

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;
use \Selene\Components\Common\Helper\ListHelper;
use \Selene\Components\Common\Traits\Getter;

/**
 * @class Dispatcher implements DispatcherInterface
 * @see DispatcherInterface
 *
 * @package Selene\Components\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Dispatcher implements DispatcherInterface, ContainerAwareInterface
{
    use Getter, ContainerAwareTrait;

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
     * Create a new event dispatcher instance.
     *
     * @param ContainerInterace $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->sorted = [];
        $this->handlers = [];
        $this->container = $container;
    }

    /**
     * Attach an eventhandler
     *
     * @param string|array $events       the event name
     * @param mixed        $eventHandler a callable or string `$service@method`
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
     * addListener
     *
     * @param string $event
     * @param EventListenerInterface $listener
     * @param void $priority
     *
     * @return void
     */
    public function addListener($event, EventListenerInterface $listener, $priority = 0)
    {
        $this->bindEvent($event, $listener, $priority);
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
     * Detach an eventhandler from an event
     *
     * @param array|string $events       the event name
     * @param mixed        $eventHanlder the eventhandler
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
     * addObserver
     *
     * @param SubscriberInterface $subscriber
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
     * removeSubscriber
     *
     * @param SubscriberInterface $subscriber
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
     * @param bool $stopOnFirstResult stop fireing if first result was found
     *
     * @return array the event results;
     */
    public function dispatch($event, $parameters = null, $stopOnFirstResult = false)
    {
        if (!isset($this->handlers[$event])) {
            return;
        }

        $results = [];

        if ($isEvent = ($parameters instanceof EventInterface)) {
            $parameters->setEventDispatcher($this);
            $parameters->setEventName($event);
        }

        foreach ($this->getSorted($event) as $i => $handlers) {

            if (!$this->doDispatch($handlers, $parameters, $isEvent, $results, $stopOnFirstResult)) {
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
    public function until($event, $parameters = [])
    {
        return $this->dispatch($event, $parameters, true);
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
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid event handler "%s".',
                    is_string($eventHandler) ? $eventHandler : (is_object($eventHandler) ?
                    get_class($eventHandler) : gettype($eventHandler))
                )
            );
        }

        list($service, $method) = array_pad(explode(static::EVTHANDLER_SEPARATOR, $eventHandler), 2, null);

        if (null === $this->container) {
            throw new \InvalidArgumentException(
                sprintf('Cannot set a service "%s" as handler, no service container is set.', $service)
            );
        }

        if (!$this->container->has($service)) {
            throw new \InvalidArgumentException(sprintf('A service with id "%s" is not defined.', $service));
        }

        return [$service, $method];
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

        $object = $this->container->get($service);

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
        if (is_array(current($eventSubscriptions))) {
            foreach ($eventSubscriptions as $subscription) {
                $this->listSubscriptions($subscriber, $event, $subscription, $list);
            }

            return $list;
        }

        list($method, $priority) = array_pad($eventSubscriptions, 2, 10);

        $eventHandler = [$subscriber, $method];

        $list[] = [$event, [$subscriber, $method], $priority];

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
     * @param array $handlers
     * @param mixed $params
     * @param mixed $isEvent
     * @param mixed $results
     * @param mixed $stopOnFirstResult
     *
     * @return boolean
     */
    private function doDispatch(array $handlers, $params, $isEvent, &$results = [], $stopOnFirstResult = false)
    {
        foreach ($handlers as $index => $handler) {

            if ($isEvent && $params->isPropagationStopped()) {
                break;
            }

            $res = $this->callListener($handler, $params);

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
    private function callListener($eventHandler, $parameters)
    {
        $eventHandler = $this->resolveHandler($eventHandler);

        if ($eventHandler instanceof EventListenerInterface) {
            return $eventHandler->handleEvent($parameters);
        } else {
            return call_user_func_array($eventHandler, !is_array($parameters) ? [$parameters] : $parameters);
        }
    }

    /**
     * Sort eventhandler by their priority.
     *
     * @return array
     */
    private function getSorted($event)
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

        foreach ($this->handlers[$event] as $priority => $handlers) {
            foreach ($handlers as $index => &$handler) {

                if ($eventHandler === $handler) {
                    unset($this->handlers[$event][$priority][$index]);
                    break;
                }
            }
        }

        return true;
    }
}
