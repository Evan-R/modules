<?php

/**
 * This File is part of the Selene\Module\Routing\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Event;

/**
 * @class RouterEvents
 * @package Selene\Module\Routing\Events
 * @version $Id$
 */
class RouterEvents
{
    const PREFIX        = 'router_event.';
    const MATCHED       = 'router_event.mached';
    const DISPATCH      = 'router_event.dispatch';
    const DISPATCHED    = 'router_event.dispatched';
    const FILTER_BEFORE = 'router_event.filter_before';
    const FILTER_AFTER  = 'router_event.filter_before';
    const NOT_FOUND     = 'router_event.not_fount';
    const ABORT         = 'router_event.abort';

    private function __construct()
    {
    }
}
