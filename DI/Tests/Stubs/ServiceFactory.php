<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Stubs;

/**
 * @class FooServiceFactory
 * @package Selene\Components\DI\Tests\Stubs
 * @version $Id$
 */
class ServiceFactory
{
    public static function makeFoo($class, array $options = [])
    {
        return new $class($options);
    }

    public static function makeBar($class, FooService $foo)
    {
        return new $class($foo);
    }
}
