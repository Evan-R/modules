<?php

/**
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI;

/**
 * @interface ParameterInterface
 * @package Selene\Module\DI
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
