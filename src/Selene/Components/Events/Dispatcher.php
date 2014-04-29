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

/**
 * @class Dispatcher implements DispatcherInterface
 * @see DispatcherInterface
 *
 * @package Selene\Components\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Dispatcher implements DispatcherInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * events
     *
     * @var array;
     */
    private $handlers = [];

    /**
     * observers
     *
     * @var array
     */
    private $observers = [];

    /**
     * sorted
     *
     * @var array
     */
    private $sorted = [];

    /**
     * Create a new event dispatcher instance.
     *
     * @param ContainerInterace $container
     *
     * @access public
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->sorted = [];
        $this->handler = [];
        $this->observers = [];
        $this->container = $container;
    }

    /**
     * Attach an eventhandler
     *
     * @param string $event        the event name
     * @param mixed  $eventHandler a callable or string `$service@method`
     * @param int    $priority     the priority with the handler being called
     * (handlers with higher values will be called first)
     *
     * @access public
     * @return void
     */
    public function on($event, $eventHandler, $priority = null)
    {
        $service = null;
        $method = null;

        if (null !== $eventHandler && !is_callable($eventHandler)) {
            if ($this->isCallableService($eventHandler)) {
                extract($this->getEventHandlerFromServiceString($event, $eventHandler));
            } else {
                throw new \InvalidArgumentException(
                    sprintf('%s::on() expects argument 2 to be valid callback', __CLASS__)
                );
            }
        }
        $this->bindEvent($event, $eventHandler, $priority, $service, $method);
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
     * @access public
     * @return void
     */
    public function once($event, $eventHandler, $priority = 10)
    {
        $service = null;
        $method = null;

        if (!is_callable($eventHandler)) {
            extract($this->getEventHandlerFromServiceString($event, $eventHandler));
        }

        $handler = $eventHandler;

        $eventHandler = function () use ($event, &$handler, &$eventHandler) {
            $dispatched = call_user_func_array($handler, func_get_args());
            $this->off($event, $eventHandler);
        };

        $this->bindEvent($event, $eventHandler, $priority, $service, $method);
    }

    /**
     * Detach an eventhandler from an event
     *
     * @param string $event        the event name
     * @param mixed  $eventHanlder the eventhandler
     *
     * @access public
     * @return void
     */
    public function off($event, $eventHandler = null)
    {
        if (!is_null($eventHandler) && $this->detachEventByHandler($event, $eventHandler)) {
            return;
        }

        unset($this->handlers[$event]);
    }

    /**
     * addObserver
     *
     * @param mixed $observer
     *
     * @access public
     * @return mixed
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
     * @param mixed $param
     *
     * @access public
     * @return mixed
     */
    public function removeSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber::getSubscriptions() as $event => $handles) {
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
     * @access public
     * @return array the event results;
     */
    public function dispatch($event, $parameters = [], $stopOnFirstResult = false)
    {
        if (!isset($this->handlers[$event])) {
            return;
        }

        $results = [];
        $this->sort($event);

        if ($isEvent = $parameters instanceof EventInterface) {
            $parameters->setName($event);
        }

        foreach ($this->handlers[$event] as $i => $handler) {

            extract($handler);

            if ($isEvent && $parameters->isPropagationStopped()) {
                break;
            }

            $res = call_user_func_array($eventHandler, !is_array($parameters) ? [$parameters] : $parameters);

            $results[] = $res;

            // stop on first result;
            if (!is_null($res) && $stopOnFirstResult) {
                break;
            }
        }

        return $results;
    }

    /**
     * Dispatches an event until the first response happened.
     *
     * @see Dispatcher#dispatch()
     *
     * @access public
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
     * @access public
     * @return array
     */
    public function getEventHandlers($event = null)
    {
        if (is_null($event)) {
            $handlers = [];

            foreach ($this->handlers as $handler) {
                $stack = ['stack' => &$handlers] + arrayPluck('eventHandler', $handler);
                call_user_func_array('array_push', $stack);
            }

            return $handlers;
        }

        if (isset($this->handlers[$event])) {
            $this->sort($event);

            return arrayPluck('eventHandler', $this->handlers[$event]);
        }

        return [];
    }

    /**
     * isCallableService
     *
     * @param mixed $eventHandler
     *
     * @access private
     * @return bool
     */
    private function isCallableService($eventHandler)
    {
        return is_string($eventHandler) && 0 === strpos($eventHandler, ContainerInterface::SERVICE_REF_INDICATOR);
    }

    /**
     * listObserverSubscriptions
     *
     * @param mixed $observer
     * @param mixed $event
     * @param array $eventSubscriptions
     *
     * @access protected
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

        $list[] = compact('event', 'eventHandler', 'priority');

        return $list;
    }

    /**
     * Bind the Eventhandler to the dispatcher
     *
     * @param string    $event        the event name
     * @param callable  $eventHandler a callable
     * @param int       $priority     event fire priority
     *
     * @access protected
     * @return void
     */
    private function bindEvent($event, callable $eventHandler, $priority = null, $service = null, $method = null)
    {
        $handler = compact('event', 'priority');
        $handler['eventHandler'] =& $eventHandler;

        if (null === $priority) {
            $priority = $this->findPriority($event);
        }

        if (!is_null($service)) {
            $handler['uses'] = implode(static::EVTHANDLER_SEPARATOR, [$service, $method]);
        }

        unset($this->sorted[$event]);

        $this->handlers[$event][] = $handler;
    }

    /**
     * findPriority
     *
     * @param mixed $event
     *
     * @access private
     * @return mixed
     */
    private function findPriority($event)
    {
        if (!$this->sorted) {
            $this->sort($event);
        }

        if (isset($this->handlers[$event])) {
            $handler = $this->handlers[$event][count($this->handlers[$event]) - 1];
            return $handler['priority'] - 1;
        }

        return 0;
    }

    /**
     * Sort eventhandler by their priority.
     *
     * @access protected
     * @return mixed
     */
    private function sort($event)
    {
        if (isset($this->sorted[$event]) || !isset($this->handlers[$event])) {
            return;
        }

        $comparator = function ($a, $b) {
            return $a['priority'] > $b['priority'] ? -1 : 1;
        };

        usort($this->handlers[$event], $comparator);

        $this->sorted[$event] = true;
    }

    /**
     * Finds a matching handler and unsets the handler for the given event.
     *
     * @param string    $event        the event name
     * @param callable  $eventHandler the event handler
     *
     * @access protected
     * @return boolean always returns true
     */
    private function detachEventByHandler($event, $eventHandler)
    {
        if ($isService = $this->isCallableService($eventHandler)) {
            $classHandler = implode(
                static::EVTHANDLER_SEPARATOR,
                $this->extractService($eventHandler)
            );
        }

        foreach ($this->handlers[$event] as $index => &$handler) {
            if ($isService && isset($handler['uses']) && $classHandler === $handler['uses']) {
                unset($this->handlers[$event][$index]);
            } elseif ($eventHandler === $handler['eventHandler']) {
                unset($this->handlers[$event][$index]);
            }
        }

        return true;
    }

    /**
     * getEventHandlerFromServiceString
     *
     * @param mixed $eventHandler
     *
     * @access protected
     * @return array
     */
    private function getEventHandlerFromServiceString($event, $eventHandler)
    {
        if (is_string($eventHandler)) {
            extract($this->extractService($eventHandler));

            if (!$this->container) {
                throw new \InvalidArgumentException(
                    'Dispatcher tried to call service from service container, but no service container was found'
                );
            }

            $eventHandler = function () use ($service, $method) {
                return call_user_func_array([$this->container->getService($service), $method], func_get_args());
            };

            return compact('eventHandler', 'service', 'method');
        }

        throw new \InvalidArgumentException('Eventhandler is invalid');
    }

    /**
     * extractClass
     *
     * @param mixed $eventHandler
     *
     * @access protected
     * @return array
     */
    private function extractService($eventHandler)
    {
        list($service, $method) = array_pad(
            explode(static::EVTHANDLER_SEPARATOR, $eventHandler),
            2,
            'handleEvent'
        );

        $service = substr($service, strlen(ContainerInterface::SERVICE_REF_INDICATOR));
        return compact('service', 'method');
    }
}
