<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing;

use \Symfony\Component\HttpFoundation\Request;
use \Selene\Components\Events\DispatcherInterface;

/**
 * @class RouterInterface
 * @package Selene\Components\Routing
 * @version $Id$
 */
interface RouterInterface
{
    public function setEventDispatcher(DispatcherInterface $events);

    public function getRoutes();

    public function getMatcher();

    public function dispatch(Request $request);

    public function findRoute(Request $request);

    public function registerFilter($name, $filter);

    public function boot(DispatcherInterface $events = null);
}
