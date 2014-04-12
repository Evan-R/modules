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

/**
 * @class ResolverInterface
 * @package Selene\Components\Routing\Controller
 * @version $Id$
 */
interface ResolverInterface
{
    public function find($action, $method);
}
