<?php

/**
 * This File is part of the Selene\Components\DI\Definition package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Definition;

/**
 * @class FlagInterface
 * @package Selene\Components\DI\Definition
 * @version $Id$
 */
interface FlagInterface
{
    public function get($argument, $default = null);

    public function getName();
}
