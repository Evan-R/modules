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
 * @class AbstractService
 * @package Selene\Components\DI\Tests\Stubs
 * @version $Id$
 */
abstract class AbstractService
{
    public function __construct(FooService $foo, $argA = 0, $argB = null)
    {
        $this->foo = $foo;
    }
}
