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

use \Selene\Module\Routing\Matchers\MatchContext;
use \Selene\Module\Routing\Event\RouteDispatched;

/**
 * @interface DispatcherInterface
 * @package Selene\Module\Routing\Controller
 * @version $Id$
 */
interface DispatcherInterface
{
    public function dispatch(MatchContext $context, RouteDispatched $event = null);
}
