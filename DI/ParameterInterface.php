<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI;

/**
 * @interface ParameterInterface
 * @package Selene\Components\DI
 * @version $Id$
 */
interface ParameterInterface extends \ArrayAccess
{
    public function set($param, $value);

    public function get($param);

    public function has($param);

    public function remove($param);

    public function all();

    public function merge(ParameterInterface $parameters);
}
