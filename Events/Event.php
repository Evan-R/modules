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

use \Selene\Components\Common\Helper\StringHelper;

/**
 * @class Event
 * @package Selene\Components\Events
 * @version $Id$
 */
class Event implements EventInterface
{
    /**
     * name
     *
     * @var string
     */
    private $eventName;

    /**
     * isStopped
     *
     * @var boolean
     */
    private $isStopped;

    /**
     * eventDispatcher
     *
     * @var DispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Sets the event name.
     *
     * @param mixed $name
     *
     * @access public
     * @return void
     */
    public function setEventName($name)
    {
        $this->eventName = $name;
    }

    /**
     * Gets the event name
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName ?: $this->getBaseEventName();
    }

    /**
     * Stops the event from beeing further propagated.
     *
     * @return void
     */
    public function stopPropagation()
    {
        $this->isStopped = true;
    }

    /**
     * Checks if the event propagation is stopped.
     *
     * @return boolean
     */
    public function isPropagationStopped()
    {
        return (boolean)$this->isStopped;
    }

    /**
     * setDispatcher
     *
     * @param DispatcherInterface $dispatcher
     *
     * @return void
     */
    public function setEventDispatcher(DispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * getDispatcher
     *
     * @return DispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * getBaseEventName
     *
     * @return string
     */
    private function getBaseEventName()
    {
        $name = basename(strtr(get_class($this), ['\\' => '/']));

        return StringHelper::strLowDash($name, '.');
    }
}
