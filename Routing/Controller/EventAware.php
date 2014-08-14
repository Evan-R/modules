<?php

/**
 * This File is part of the Selene\Module\Routing\Controller package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Controller;

use \Selene\Module\Routing\Events\RouteDispatchEvent;

/**
 * @interface EventAware
 * @package Selene\Module\Routing\Controller
 * @version $Id$
 */
interface EventAware
{
    public function setEvent(RouteDispatchEvent $event);
}
