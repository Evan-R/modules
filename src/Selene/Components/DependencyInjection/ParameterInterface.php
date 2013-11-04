<?php

/**
 * This File is part of the Selene\Components\DependencyInjection package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DependencyInjection;

/**
 * @interface ParameterInterface
 * @package Selene\Components\DependencyInjection
 * @version $Id$
 */
interface ParameterInterface
{
    public function set($param, $value);

    public function get($param, $default = null);
}
