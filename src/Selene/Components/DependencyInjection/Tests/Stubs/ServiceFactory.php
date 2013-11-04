<?php

/**
 * This File is part of the Selene\Components\DependencyInjection\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DependencyInjection\Tests\Stubs;

/**
 * @class FooServiceFactory
 * @package Selene\Components\DependencyInjection\Tests\Stubs
 * @version $Id$
 */
class ServiceFactory
{
    public static function makeFoo(array $options = [])
    {
        return new FooService($options);
    }

    public static function makeBar(FooService $foo)
    {
        return new BarService($foo);
    }
}
