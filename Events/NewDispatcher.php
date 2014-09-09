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
 * @class NewDispatcher
 *
 * @package Selene\Module\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class NewDispatcher extends Dispatcher
{
    protected $listeners;

    protected function bindEvent($event, &$eventHandler, $priority = 0)
    {
        $this->listeners[$event][spl_object_hash][$priority] = &$eventHandler;
    }

    protected function &getSorted($event)
    {
        if (!isset($this->sorted[$event])) {
            uksort($this->handlers[$event], function ($handler) {
                return -1;
            });
        }

        $this->sorted[$event] = true;

        return $this->handlers[$event];
    }
}
