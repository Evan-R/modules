<?php

/*
 * This File is part of the Selene\Module\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events;

/**
 * @class EventName
 *
 * @package Selene\Module\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class EventName
{
    /**
     * event
     *
     * @var EventInterface
     */
    private $event;

    /**
     * Construct.
     *
     * @param EventInterface $event
     */
    public function __construct(EventInterface $event)
    {
        $this->event = $event;
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        if (null !== ($name = $this->event->getEventName())) {
            return $name;
        }

        return $this->parseEventName();
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * getBaseEventName
     *
     * @return string
     */
    private function parseEventName()
    {
        $name = basename(strtr(get_class($this->event), ['\\' => '/']));

        return StringHelper::strLowDash($name, '.');
    }
}
