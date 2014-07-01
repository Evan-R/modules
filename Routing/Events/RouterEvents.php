<?php

/**
 * This File is part of the Selene\Components\Routing\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Events;

/**
 * @class RouterEvents
 * @package Selene\Components\Routing\Events
 * @version $Id$
 */
class RouterEvents
{
    const DISPATCH      = 'router.dispatch';
    const DISPATCHED    = 'router.route_dispatched';
    const FILTER_BEFORE = 'router.route_filter_before';
    const FILTER_AFTER  = 'router.route_filter_before';
    const NOT_FOUND     = 'router.route_not_fount';
    const ABORT         = 'router.abort';

    private function __construct()
    {
    }
}
