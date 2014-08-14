<?php

/**
 * This File is part of the Selene\Module\Routing\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Events;

/**
 * @class RouterEvents
 * @package Selene\Module\Routing\Events
 * @version $Id$
 */
class RouterEvents
{
    const DISPATCH      = 'router.route_dispatch';
    const DISPATCHED    = 'router.route_dispatched';
    const FILTER_BEFORE = 'router.route_filter_before';
    const FILTER_AFTER  = 'router.route_filter_before';
    const NOT_FOUND     = 'router.route_not_fount';
    const ABORT         = 'router.route_abort';

    private function __construct()
    {
    }
}
