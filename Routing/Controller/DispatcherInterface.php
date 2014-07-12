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

use \Selene\Components\Routing\Matchers\MatchContext;

/**
 * @interface DispatcherInterface
 * @package Selene\Components\Routing\Controller
 * @version $Id$
 */
interface DispatcherInterface
{
    public function dispatch(MatchContext $context);
}
