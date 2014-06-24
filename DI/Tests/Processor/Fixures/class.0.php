<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Processor\Fixures package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

/**
 * @package Selene\Components\DI\Tests\Processor\Fixures
 * @version $Id$
 */
class FooFactory
{
    public static function make($class)
    {
        return new $class;
    }
}
