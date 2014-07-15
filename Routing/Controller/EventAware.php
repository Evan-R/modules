<?php

/**
 * This File is part of the Selene\Components\Routing\Controller package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Controller;

use \Selene\Components\Routing\Events\RouteDispatchEvent;

/**
 * @interface EventAware
 * @package Selene\Components\Routing\Controller
 * @version $Id$
 */
interface EventAware
{
    public function setEvent(RouteDispatchEvent $event);
}
